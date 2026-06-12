-- Pass 2b: Gannett / GateHouse / USA Today metros (Kat crosswalk, includes 1-quiz sites)
-- Hosts sourced from Kat CSV corporate owner field — excludes wickedlocal (see Pass 2a).
-- Regenerate host list: python3 sql/build-pass-2b-gannett-hosts.py
--
-- Expect ~91 rows locally (all Kat Gannett-family metros still in embed DB)
--
-- ADMINNEO IMPORTANT:
-- Temporary tables disappear if you Execute Section A, then Execute Section B separately.
-- Error: Table 'local.pass2b_kat_gannett_hosts' doesn't exist
-- FIX: Copy lines 11–135 (Section A + B together) into the SQL box and Execute ONCE.
--      AdminNeo will show the SELECT results from Section B at the bottom.

-- ---------------------------------------------------------------------------
-- A) Create temp allowlist (91 hosts from Kat CSV)
-- RUN TOGETHER WITH B — do not Execute B alone in a new query
-- ---------------------------------------------------------------------------
DROP TEMPORARY TABLE IF EXISTS pass2b_kat_gannett_hosts;
CREATE TEMPORARY TABLE pass2b_kat_gannett_hosts (
  normalized_host VARCHAR(191) NOT NULL PRIMARY KEY
);

INSERT INTO pass2b_kat_gannett_hosts (normalized_host) VALUES
  ('app.com'),
  ('ardmoreite.com'),
  ('argusleader.com'),
  ('arkansasnews.com'),
  ('azcentral.com'),
  ('barnesville-enterprise.com'),
  ('barnstablepatriot.com'),
  ('battlecreekenquirer.com'),
  ('baxterbulletin.com'),
  ('beaconjournal.com'),
  ('buckscountycouriertimes.com'),
  ('burlingtoncountytimes.com'),
  ('capecodtimes.com'),
  ('citizen-times.com'),
  ('clarionledger.com'),
  ('columbusalive.com'),
  ('columbusparent.com'),
  ('courier-journal.com'),
  ('courierpress.com'),
  ('currentargus.com'),
  ('daily-times.com'),
  ('dailycommercial.com'),
  ('delawareonline.com'),
  ('delmarvanow.com'),
  ('dnj.com'),
  ('ellwoodcityledger.com'),
  ('enterprisenews.com'),
  ('eu.goerie.com'),
  ('eu.ocala.com'),
  ('eu.tuscaloosanews.com'),
  ('fdlreporter.com'),
  ('floridatoday.com'),
  ('fosters.com'),
  ('freep.com'),
  ('gainesville.com'),
  ('greatfallstribune.com'),
  ('greenbaypressgazette.com'),
  ('greenvilleonline.com'),
  ('heraldnews.com'),
  ('jdnews.com'),
  ('jsonline.com'),
  ('kinston.com'),
  ('kitsapsun.com'),
  ('knoxnews.com'),
  ('lancastereaglegazette.com'),
  ('lansingstatejournal.com'),
  ('ldnews.com'),
  ('lohud.com'),
  ('milforddailynews.com'),
  ('moscowvillager.com'),
  ('mycouriertribune.com'),
  ('mytownneo.com'),
  ('newbernsj.com'),
  ('newportri.com'),
  ('news-press.com'),
  ('newsherald.com'),
  ('northjersey.com'),
  ('packersnews.com'),
  ('patriotledger.com'),
  ('pnj.com'),
  ('postcrescent.com'),
  ('poughkeepsiejournal.com'),
  ('pressconnects.com'),
  ('providencejournal.com'),
  ('publicopiniononline.com'),
  ('record-courier.com'),
  ('seacoastonline.com'),
  ('sheboyganpress.com'),
  ('southcoasttoday.com'),
  ('starnewsonline.com'),
  ('stevenspointjournal.com'),
  ('swtimes.com'),
  ('tauntongazette.com'),
  ('tcpalm.com'),
  ('tennessean.com'),
  ('thecarbondalenews.com'),
  ('thedailyjournal.com'),
  ('thegardnernews.com'),
  ('thenorthwestern.com'),
  ('theolympian.com'),
  ('thetimesherald.com'),
  ('thisweeknews.com'),
  ('timesreporter.com'),
  ('tricountyindependent.com'),
  ('tuscaloosanews.com'),
  ('usatoday.com'),
  ('vcstar.com'),
  ('visaliatimesdelta.com'),
  ('wayneindependent.com'),
  ('wltribune.com'),
  ('ydr.com');


-- ---------------------------------------------------------------------------
-- B) Simple version — embed sites matching Kat Gannett-family hosts
-- ---------------------------------------------------------------------------
SELECT
  s.embed_site_id,
  s.embed_site_url,
  COUNT(DISTINCT eq.quiz_id) AS quiz_count,
  COUNT(DISTINCT q.quiz_owner) AS owner_count,
  k.normalized_host
FROM wp_enp_embed_site s
INNER JOIN wp_enp_embed_quiz eq
  ON eq.embed_site_id = s.embed_site_id
INNER JOIN wp_enp_quiz q
  ON q.quiz_id = eq.quiz_id
INNER JOIN pass2b_kat_gannett_hosts k
  ON k.normalized_host = REPLACE(
    SUBSTRING_INDEX(
      REPLACE(REPLACE(LOWER(TRIM(s.embed_site_url)), 'https://', ''), 'http://', ''),
      '/', 1
    ),
    'www.', ''
  )
WHERE LOWER(s.embed_site_url) NOT LIKE '%localhost%'
GROUP BY s.embed_site_id, s.embed_site_url, k.normalized_host
HAVING quiz_count >= 1
ORDER BY quiz_count DESC, s.embed_site_url;


-- ---------------------------------------------------------------------------
-- C) Non-spam quiz filter
-- ---------------------------------------------------------------------------
SELECT
  s.embed_site_id,
  s.embed_site_url,
  COUNT(DISTINCT eq.quiz_id) AS quiz_count,
  COUNT(DISTINCT q.quiz_owner) AS owner_count,
  k.normalized_host,
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
INNER JOIN pass2b_kat_gannett_hosts k
  ON k.normalized_host = REPLACE(
    SUBSTRING_INDEX(
      REPLACE(REPLACE(LOWER(TRIM(s.embed_site_url)), 'https://', ''), 'http://', ''),
      '/', 1
    ),
    'www.', ''
  )
LEFT JOIN wp_usermeta spam_cap
  ON spam_cap.user_id = q.quiz_owner
 AND spam_cap.meta_key = 'wp_capabilities'
WHERE LOWER(s.embed_site_url) NOT LIKE '%localhost%'
GROUP BY s.embed_site_id, s.embed_site_url, k.normalized_host
HAVING non_spam_quizzes >= 1
ORDER BY quiz_count DESC, s.embed_site_url;


-- ---------------------------------------------------------------------------
-- D) Quick count check (run after section A in same session)
-- ---------------------------------------------------------------------------
SELECT COUNT(*) AS gannett_family_sites
FROM (
  SELECT s.embed_site_id
  FROM wp_enp_embed_site s
  INNER JOIN wp_enp_embed_quiz eq
    ON eq.embed_site_id = s.embed_site_id
  INNER JOIN pass2b_kat_gannett_hosts k
    ON k.normalized_host = REPLACE(
      SUBSTRING_INDEX(
        REPLACE(REPLACE(LOWER(TRIM(s.embed_site_url)), 'https://', ''), 'http://', ''),
        '/', 1
      ),
      'www.', ''
    )
  GROUP BY s.embed_site_id
  HAVING COUNT(DISTINCT eq.quiz_id) >= 1
) t;
