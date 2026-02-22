UPDATE animals SET status='Available for Adoption' WHERE id <= 5;
SELECT id, name, type, age, gender, status FROM animals WHERE status='Available for Adoption' LIMIT 5;