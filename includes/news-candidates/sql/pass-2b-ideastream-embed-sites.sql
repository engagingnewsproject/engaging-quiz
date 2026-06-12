-- Pass 2b: Ideastream main service domains only (includes 1-quiz embeds)
-- Run in AdminNeo: paste ONE query block at a time (no mysql shell wrapper).
--
-- IMPORTANT: Do NOT use a broad LIKE '%ideastream.org%' — the DB has 100+ typo/scraper
-- subdomains (www.wviz.ideastream.org, www.mathmess.ideastream.org, etc.).
-- This pass uses an explicit allowlist of real Ideastream properties.

-- ---------------------------------------------------------------------------
-- 1) Simple version — allowlisted Ideastream hosts only
-- Expect 4 rows locally (ideastream.org, wcpn, wclv, news)
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
  AND REPLACE(
    SUBSTRING_INDEX(
      REPLACE(REPLACE(LOWER(TRIM(s.embed_site_url)), 'https://', ''), 'http://', ''),
      '/', 1
    ),
    'www.', ''
  ) IN (
    'ideastream.org',
    'wcpn.ideastream.org',
    'wclv.ideastream.org',
    'news.ideastream.org'
  )
GROUP BY s.embed_site_id, s.embed_site_url
HAVING quiz_count >= 1
ORDER BY quiz_count DESC, s.embed_site_url;


-- ---------------------------------------------------------------------------
-- 2) Non-spam quiz filter
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
  AND REPLACE(
    SUBSTRING_INDEX(
      REPLACE(REPLACE(LOWER(TRIM(s.embed_site_url)), 'https://', ''), 'http://', ''),
      '/', 1
    ),
    'www.', ''
  ) IN (
    'ideastream.org',
    'wcpn.ideastream.org',
    'wclv.ideastream.org',
    'news.ideastream.org'
  )
GROUP BY s.embed_site_id, s.embed_site_url
HAVING non_spam_quizzes >= 1
ORDER BY quiz_count DESC, s.embed_site_url;


-- ---------------------------------------------------------------------------
-- 3) Sanity check — how many ideastream.org hosts exist total (should be ~120+)
--    vs allowlist (should be ~4). Run separately to see why broad matching fails.
-- ---------------------------------------------------------------------------
SELECT COUNT(DISTINCT s.embed_site_id) AS all_ideastream_subdomains
FROM wp_enp_embed_site s
INNER JOIN wp_enp_embed_quiz eq
  ON eq.embed_site_id = s.embed_site_id
WHERE LOWER(s.embed_site_url) LIKE '%ideastream.org%';
