-- Seed initial data for countries

-- Clear existing data
TRUNCATE TABLE countries;

-- Insert test data
INSERT INTO countries (short_name, full_name, iso_alpha2, iso_alpha3, iso_numeric, population, square) VALUES
('Russia', 'Russian Federation', 'RU', 'RUS', '643', 146150789, 17125191.00),
('USA', 'United States of America', 'US', 'USA', '840', 331900000, 9833520.00),
('China', 'People''s Republic of China', 'CN', 'CHN', '156', 1439323776, 9596961.00),
('Germany', 'Federal Republic of Germany', 'DE', 'DEU', '276', 83240525, 357022.00),
('Japan', 'Japan', 'JP', 'JPN', '392', 125800000, 377975.00),
('United Kingdom', 'United Kingdom of Great Britain and Northern Ireland', 'GB', 'GBR', '826', 67886011, 242495.00),
('France', 'French Republic', 'FR', 'FRA', '250', 67413000, 643801.00),
('India', 'Republic of India', 'IN', 'IND', '356', 1393409038, 3287263.00),
('Brazil', 'Federative Republic of Brazil', 'BR', 'BRA', '076', 213993437, 8515767.00),
('Canada', 'Canada', 'CA', 'CAN', '124', 38005238, 9984670.00);

-- Verify data
SELECT COUNT(*) as total_countries FROM countries;