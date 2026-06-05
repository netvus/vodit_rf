CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fio VARCHAR(255) NOT NULL,
    birth_date DATE NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    role ENUM('user','admin') NOT NULL DEFAULT 'user'
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    transport_type VARCHAR(255) NOT NULL,
    start_date DATE NOT NULL,
    payment_method VARCHAR(255) NOT NULL,
    status ENUM('new','learning','done') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_applications_user_id (user_id),
    INDEX idx_applications_status (status),
    INDEX idx_applications_start_date (start_date),
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    application_id INT NOT NULL,
    review_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reviews_user_id (user_id),
    INDEX idx_reviews_application_id (application_id),
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
    FOREIGN KEY (application_id) REFERENCES applications(id)
    ON DELETE CASCADE
);

INSERT INTO users
(login,password,fio,birth_date,phone,email,role)
VALUES
(
'Admin26',
'$2y$10$5xa6QwLImVkR3TPbXWOvcOexulT7NfFKfFND/Ju.cONJm.rVUmKiO',
'Администратор',
'2000-01-01',
'+79999999999',
'admin@test.ru',
'admin'
);
