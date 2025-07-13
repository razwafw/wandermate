<?php
include 'env.php';

// Database connection
$conn = new mysqli('localhost', 'projec15_root', '@kaesquare123', 'projec15_wandermate');
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Fetch packages with their first image
$packages = [];
$sql = 'SELECT p.id, p.name, p.description, p.price / p.group_size as price_per_person, (SELECT url FROM images WHERE package_id = p.id LIMIT 1) AS image_url FROM packages p';
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>WanderMate - Travel Packages</title>

    <!-- Global Styles -->
    <style>
        html {
            scroll-behavior: smooth;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #fff;
            color: #000;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .btn {
            display: inline-block;
            background-color: midnightblue;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #1a1a5a;
            color: #fff;
        }

        .btn-full {
            width: 100%;
            text-align: center;
            padding: 15px;
            font-size: 1.1rem;
        }

        .btn-outline {
            background-color: transparent;
            color: midnightblue;
            border: 1px solid midnightblue;
        }

        .btn-outline:hover {
            background-color: rgba(25, 25, 112, 0.1);
            color: midnightblue;
        }

        button[disabled] {
            background-color: #cccccc !important;
            color: #666666 !important;
            cursor: not-allowed;
            opacity: 0.7;
        }

        button[disabled]:hover {
            background-color: #cccccc !important;
            color: #666666 !important;
        }
    </style>

    <!-- Page Styles-->
    <style>
        .page-header {
            background-color: #f8f9fa;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 20px;
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: midnightblue;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #666;
            max-width: 700px;
            margin: 0 auto;
        }

        /* Search Bar Styles */
        .search-container {
            max-width: 600px;
            margin: 0 auto 40px;
            position: relative;
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 30px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .search-input {
            padding: 12px 20px;
            border: 1px solid #ddd;
            border-radius: 30px;
            outline: none;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
        }

        .search-input:focus {
            border-color: midnightblue;
            box-shadow: 0 0 8px rgba(25, 25, 112, 0.2);
        }

        .search-btn {
            background: none;
            border: none;
            cursor: pointer;
            position: absolute;
            width: fit-content;
            height: fit-content;
            right: 30px;
            color: #666;
        }

        .search-btn:hover {
            color: midnightblue;
        }

        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .package-card {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .package-image {
            height: 200px;
            background-size: cover;
            background-position: center;
        }

        .package-content {
            padding: 20px;
        }

        .package-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: midnightblue;
        }

        .package-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .package-price {
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }

        .price-highlight {
            color: midnightblue;
            font-size: 1.2rem;
        }

        .package-action {
            text-align: center;
        }

        @media (max-width: 768px) {
            .packages-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
    </style>

    <!-- Font Awesome -->
    <script
        src="https://kit.fontawesome.com/c880a1b0f6.js"
        crossorigin="anonymous"
    ></script>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Page Content -->
    <main>
        <div class="page-header">
            <div class="container">
                <h1>Explore Our Travel Packages</h1>
                <p>Discover carefully curated travel experiences that combine adventure, comfort, and unforgettable memories. From tropical getaways to cultural explorations, we have the perfect package for you.</p>
            </div>
        </div>

        <div class="container">
            <div class="search-container">
                <input
                    type="text"
                    class="search-input"
                    placeholder="Search for packages, destinations..."
                    id="package-search"
                />
                <button
                    class="search-btn"
                    onclick="document.querySelector('.search-input').focus();"
                >
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
            <script>
                // Debounce function
                function debounce(fn, delay) {
                    let timer = null;
                    return function (...args) {
                        clearTimeout(timer);
                        timer = setTimeout(() => fn.apply(this, args), delay);
                    };
                }

                // Search handler
                function handleSearch() {
                    const input = document.getElementById("package-search").value.trim().toLowerCase();
                    const cards = document.querySelectorAll(".package-card");
                    cards.forEach(card => {
                        const name = card.querySelector(".package-name").textContent.toLowerCase();
                        if (name.includes(input)) {
                            card.style.display = "";
                        } else {
                            card.style.display = "none";
                        }
                    });
                }

                document.getElementById("package-search").addEventListener("input", debounce(handleSearch, 300));
            </script>

            <div class="packages-grid">
                <?php if (count($packages) > 0): ?>
                    <?php foreach ($packages as $package): ?>
                        <div class="package-card">
                            <div
                                class="package-image"
                                style="background-image: url('<?php echo htmlspecialchars($package['image_url'] ?: 'bali.jpg'); ?>');"
                            ></div>
                            <div class="package-content">
                                <h3 class="package-name"><?php echo htmlspecialchars($package['name']); ?></h3>
                                <p class="package-description"><?php echo htmlspecialchars($package['description']); ?></p>
                                <p class="package-price">From
                                    <span class="price-highlight">$<?php echo number_format($package['price_per_person']); ?></span> per person
                                </p>
                                <div class="package-action">
                                    <a
                                        href="package-detail.php?id=<?php echo $package['id']; ?>"
                                        class="btn"
                                    >
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No packages found.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
