/* get scraped competition data gruped by country */
SELECT * FROM scraped_competitions WHERE country_id = 6;

SELECT
    c.id AS country_id,
    c.name AS country_name,
    betano.league_name AS league_name_in_betano,
    superbet.league_name AS league_name_in_superbet,
    casa_pariurilor.league_name AS league_name_in_casa_pariurilor
FROM countries AS c
         LEFT JOIN (
    SELECT sc.name AS league_name, sc.country_id
    FROM scraped_competitions AS sc
    WHERE sc.site_id = 1
) AS betano ON betano.country_id = c.id
         LEFT JOIN (
    SELECT sc.name AS league_name, sc.country_id
    FROM scraped_competitions AS sc
    WHERE sc.site_id = 2

) AS superbet ON superbet.country_id = c.id
         LEFT JOIN (
    SELECT sc.name AS league_name, sc.country_id
    FROM scraped_competitions AS sc
    WHERE sc.site_id = 3
) AS casa_pariurilor ON casa_pariurilor.country_id = c.id
WHERE c.id = 6
ORDER BY c.name ASC;


SELECT
    MAX(CASE WHEN ss.id = 1 THEN sc.name END) AS league_name_in_betano,
    MAX(CASE WHEN ss.id = 2 THEN sc.name END) AS league_name_in_superbet,
    MAX(CASE WHEN ss.id = 3 THEN sc.name END) AS league_name_in_casa_pariurilor,
    c.id AS country_id,
    c.name AS country_name
FROM scraped_competitions AS sc
         INNER JOIN countries AS c ON sc.country_id = c.id
         INNER JOIN sites_search AS ss ON sc.site_id = ss.id
GROUP BY sc.name, c.id, c.name
ORDER BY c.name ASC

/* get links GROUP BY competition_id  HAVING  COUNT(lsp.site_id) > 2 */
SELECT lsp.competition_id,
       com.name AS competition_name,
       com.alias AS competition_alias,
       countries.name as country_name,
       GROUP_CONCAT(lsp.site_id ORDER BY
            CASE
                WHEN lsp.site_id = 1 THEN 1
                WHEN lsp.site_id = 2 THEN 2
                WHEN lsp.site_id = 3 THEN 3
                ELSE 4 /* un fallback pentru site_id-uri necunoscute */
            END ASC
       ) AS site_ids,
       GROUP_CONCAT(lsp.link_league ORDER BY
            CASE
                WHEN lsp.site_id = 1 THEN 1
                WHEN lsp.site_id = 2 THEN 2
                WHEN lsp.site_id = 3 THEN 3
                ELSE 4 /* un fallback pentru link-uri necunoscute */
            END ASC
       ) AS links
FROM links_search_page AS lsp
         INNER JOIN competitions AS com ON com.id = lsp.competition_id
         INNER JOIN countries ON countries.id = com.country_id
GROUP BY lsp.competition_id, com.name, com.alias, countries.name
HAVING  COUNT(DISTINCT lsp.site_id) > 2
   AND (FIND_IN_SET('1', GROUP_CONCAT(site_id)) > 0
    AND FIND_IN_SET('2', GROUP_CONCAT(site_id)) > 0
    AND FIND_IN_SET('3', GROUP_CONCAT(site_id)) > 0);

/*extract and check data about competition*/
SELECT com.id, com.country_id, cou.name AS country_name, com.name, com.alias
FROM `competitions` AS com INNER JOIN countries AS cou ON com.country_id = cou.id
ORDER BY `country_name` ASC

/* delete some competition data  */
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE links_search_page;
TRUNCATE TABLE competitions;
SET FOREIGN_KEY_CHECKS = 1;
/* other way */
DELETE FROM competitions;
ALTER TABLE competitions AUTO_INCREMENT = 1;

DELETE FROM links_search_page;
ALTER TABLE links_search_page AUTO_INCREMENT = 1;
/* find to add competition */
SELECT lsp.link_league , c.id AS competetionID,lsp.site_id, c.name, c.alias
FROM links_search_page AS lsp
         INNER JOIN competitions AS c ON c.id = lsp.competition_id
         INNER JOIN countries AS co ON co.id = c.country_id
WHERE co.name = 'romania';

INSERT INTO `links_search_page` (`id`, `competition_id`, `site_id`, `type_game`, `link_league`, `with_data`, `scraped`, `created_at`, `updated_at`)
VALUES (NULL, '178', '1', 'football', 'https://ro.betano.com/sport/fotbal/romania/liga-1/17088/', '0', '0', NOW(), NOW())
