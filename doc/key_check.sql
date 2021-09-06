SET @data_source = 'dilovod';
SET @key = 'dilovod_product_id';

-- Пошук дубльованих записів
SELECT
    r.*, COUNT(r.new_value) AS `total`
FROM relations r
WHERE 1
  AND r.data_source = @data_source
  AND r.`key` = @key
GROUP BY new_value
HAVING COUNT(r.new_value) > 1
order BY `total` DESC
;

-- Список всіх дубльованих значень
SELECT
    r.*,
    (
        SELECT
            COUNT(r2.new_value) AS `total`
        FROM relations r2
        WHERE 1
          AND r2.data_source = @data_source
          AND r2.`key` = @key
          AND r2.new_value = r.new_value
        GROUP BY r2.new_value
    )
    AS `total`
FROM relations r
WHERE 1
  AND r.data_source = @data_source
  AND r.`key` = @key
HAVING `total` > 1
order BY new_value DESC;

-- Список всіх відношень по ключу
SELECT
    *
FROM relations r
WHERE 1
  AND r.data_source = @data_source
  AND r.`key` = @key;