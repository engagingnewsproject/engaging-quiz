-- Pass 2b: NBC / Telemundo / NECN local embed sites (includes 1-quiz stations)
-- Run in AdminNeo: paste ONE query block at a time (no mysql shell wrapper).
--
-- Matches production NBC O&O locals, NECN, and Telemundo station domains.
-- Excludes stage/dev/vendor URLs (e.g. stage.www.nbcphiladelphia.com, ots.nbcwpshield.com).

-- ---------------------------------------------------------------------------
-- 1) Simple version — NBCUniversal local station pattern
-- Expect ~12–15 NBC rows + NECN + a few Telemundo locally (varies by DB)
-- ---------------------------------------------------------------------------
SELECT
  s.embed_site_id,
  s.embed_site_url,
  COUNT(DISTINCT eq.quiz_id) AS quiz_count,
  COUNT(DISTINCT q.quiz_owner) AS owner_count
FROM wp_enp_embed_site s
INNER JOIN wp_enp_embed_quiz eq
  ON eq.embed_site_id = s.embed_site_id
INNER JOIN wp_enp_quiz q
  ON q.quiz_id = eq.quiz_id
WHERE LOWER(s.embed_site_url) NOT LIKE '%localhost%'
  AND LOWER(s.embed_site_url) NOT LIKE '%stage.%'
  AND LOWER(s.embed_site_url) NOT LIKE '%dev.%'
  AND LOWER(s.embed_site_url) NOT LIKE '%preview.%'
  AND (
    LOWER(s.embed_site_url) REGEXP '://(www\\.)?nbc[a-z0-9-]+\\.com'
    OR LOWER(s.embed_site_url) REGEXP '://(www\\.)?necn\\.com'
    OR LOWER(s.embed_site_url) REGEXP '://(www\\.)?telemundo[0-9a-z-]+\\.com'
  )
  AND LOWER(s.embed_site_url) NOT LIKE '%nbcwpshield%'
GROUP BY s.embed_site_id, s.embed_site_url
HAVING quiz_count >= 1
ORDER BY quiz_count DESC, s.embed_site_url;


-- ---------------------------------------------------------------------------
-- 2) Non-spam quiz filter (aligned with 6,008-site export)
-- ---------------------------------------------------------------------------
SELECT
  s.embed_site_id,
  s.embed_site_url,
  COUNT(DISTINCT eq.quiz_id) AS quiz_count,
  COUNT(DISTINCT q.quiz_owner) AS owner_count,
  SUM(
    CASE
      WHEN spam_cap.meta_value LIKE '%"spam_user"%' THEN 1
      ELSE 0
    END
  ) AS spam_quizzes,
  SUM(
    CASE
      WHEN spam_cap.meta_value IS NULL
        OR spam_cap.meta_value NOT LIKE '%"spam_user"%'
      THEN 1
      ELSE 0
    END
  ) AS non_spam_quizzes
FROM wp_enp_embed_site s
INNER JOIN wp_enp_embed_quiz eq
  ON eq.embed_site_id = s.embed_site_id
INNER JOIN wp_enp_quiz q
  ON q.quiz_id = eq.quiz_id
LEFT JOIN wp_usermeta spam_cap
  ON spam_cap.user_id = q.quiz_owner
 AND spam_cap.meta_key = 'wp_capabilities'
WHERE LOWER(s.embed_site_url) NOT LIKE '%localhost%'
  AND LOWER(s.embed_site_url) NOT LIKE '%stage.%'
  AND LOWER(s.embed_site_url) NOT LIKE '%dev.%'
  AND LOWER(s.embed_site_url) NOT LIKE '%preview.%'
  AND (
    LOWER(s.embed_site_url) REGEXP '://(www\\.)?nbc[a-z0-9-]+\\.com'
    OR LOWER(s.embed_site_url) REGEXP '://(www\\.)?necn\\.com'
    OR LOWER(s.embed_site_url) REGEXP '://(www\\.)?telemundo[0-9a-z-]+\\.com'
  )
  AND LOWER(s.embed_site_url) NOT LIKE '%nbcwpshield%'
GROUP BY s.embed_site_id, s.embed_site_url
HAVING non_spam_quizzes >= 1
ORDER BY quiz_count DESC, s.embed_site_url;


-- ---------------------------------------------------------------------------
-- 3) Quick count check
-- ---------------------------------------------------------------------------
SELECT COUNT(*) AS nbc_local_sites
FROM (
  SELECT s.embed_site_id
  FROM wp_enp_embed_site s
  INNER JOIN wp_enp_embed_quiz eq
    ON eq.embed_site_id = s.embed_site_id
  WHERE LOWER(s.embed_site_url) NOT LIKE '%localhost%'
    AND LOWER(s.embed_site_url) NOT LIKE '%stage.%'
    AND LOWER(s.embed_site_url) NOT LIKE '%dev.%'
    AND (
      LOWER(s.embed_site_url) REGEXP '://(www\\.)?nbc[a-z0-9-]+\\.com'
      OR LOWER(s.embed_site_url) REGEXP '://(www\\.)?necn\\.com'
      OR LOWER(s.embed_site_url) REGEXP '://(www\\.)?telemundo[0-9a-z-]+\\.com'
    )
    AND LOWER(s.embed_site_url) NOT LIKE '%nbcwpshield%'
  GROUP BY s.embed_site_id
  HAVING COUNT(DISTINCT eq.quiz_id) >= 1
) t;
