/*extrag and check data about competition*/
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
