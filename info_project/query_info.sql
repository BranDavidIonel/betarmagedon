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
