-- Pass 2a: Wicked Local embed sites (includes 1-quiz town papers)
-- Run in AdminNeo: paste ONE query block at a time (no mysql shell wrapper).
-- Tables: wp_enp_embed_site, wp_enp_embed_quiz, wp_enp_quiz, wp_usermeta

-- ---------------------------------------------------------------------------
-- 1) Simple version — wickedlocal hosts + quiz counts
-- Expect ~160 rows locally
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
WHERE LOWER(s.embed_site_url) LIKE '%wickedlocal.com%'
  AND LOWER(s.embed_site_url) NOT LIKE '%localhost%'
GROUP BY s.embed_site_id, s.embed_site_url
HAVING quiz_count >= 1
ORDER BY quiz_count DESC, s.embed_site_url;


-- ---------------------------------------------------------------------------
-- 2) Same pass, but only embeds with at least one NON-spam-user quiz
-- (matches the logic in your 6,008-site export)
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
WHERE LOWER(s.embed_site_url) LIKE '%wickedlocal.com%'
  AND LOWER(s.embed_site_url) NOT LIKE '%localhost%'
GROUP BY s.embed_site_id, s.embed_site_url
HAVING non_spam_quizzes >= 1
ORDER BY quiz_count DESC, s.embed_site_url;


-- ---------------------------------------------------------------------------
-- 3) Quick count check
-- ---------------------------------------------------------------------------
SELECT COUNT(*) AS wicked_local_sites
FROM (
  SELECT s.embed_site_id
  FROM wp_enp_embed_site s
  INNER JOIN wp_enp_embed_quiz eq
    ON eq.embed_site_id = s.embed_site_id
  WHERE LOWER(s.embed_site_url) LIKE '%wickedlocal.com%'
    AND LOWER(s.embed_site_url) NOT LIKE '%localhost%'
  GROUP BY s.embed_site_id
  HAVING COUNT(DISTINCT eq.quiz_id) >= 1
) t;
