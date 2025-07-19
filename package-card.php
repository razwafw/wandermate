<?php
global $package;
?>

<a
    class="package-card"
    href="package-detail.php?id=<?php echo $package['id']; ?>"
>
    <div class="package-img">
        <img
            src="<?php echo htmlspecialchars($package['image']); ?>"
            alt="<?php echo htmlspecialchars($package['name']); ?>"
        >
    </div>
    <div class="package-content">
        <h3 class="package-name"><?php echo htmlspecialchars($package['name']); ?></h3>
        <p class="package-description"><?php echo htmlspecialchars($package['description']); ?></p>
        <p class="package-price">From
            <span class="price-highlight">$<?php echo number_format($package['price_per_person']); ?></span> per person
        </p>
        <div class="package-action">
            <button class="btn">
                View Details
            </button>
        </div>
    </div>
</a>