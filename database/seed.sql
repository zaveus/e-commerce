BEGIN;

INSERT INTO books (
    name, author, isbn, year_published, genre,
    price, stock, description, image_url, is_featured
)
VALUES
    (
        'The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 1925,
        'Classic Fiction', 12.99, 20,
        'A novel about wealth, ambition, and the American Dream.',
        'https://covers.openlibrary.org/b/isbn/9780743273565-L.jpg', TRUE
    ),
    (
        '1984', 'George Orwell', '9780451524935', 1949,
        'Dystopian Fiction', 14.99, 15,
        'A novel about surveillance and totalitarianism.',
        'https://covers.openlibrary.org/b/isbn/9780451524935-L.jpg', TRUE
    ),
    (
        'Pride and Prejudice', 'Jane Austen', '9780141439518', 1813,
        'Romance', 10.99, 25,
        'A novel about love, society, and marriage.',
        'https://covers.openlibrary.org/b/isbn/9780141439518-L.jpg', FALSE
    ),
    (
        'To Kill a Mockingbird', 'Harper Lee', '9780061120084', 1960,
        'Classic Fiction', 13.99, 18,
        'A novel exploring justice, prejudice, and morality.',
        'https://covers.openlibrary.org/b/isbn/9780061120084-L.jpg', FALSE
    ),
    (
        'The Hobbit', 'J.R.R. Tolkien', '9780547928227', 1937,
        'Fantasy', 15.99, 12,
        'An adventure following Bilbo Baggins.',
        'https://covers.openlibrary.org/b/isbn/9780547928227-L.jpg', TRUE
    )
ON CONFLICT (isbn) DO NOTHING;

COMMIT;