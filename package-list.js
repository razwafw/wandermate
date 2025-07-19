function debounce(fn, delay) {
    let timer = null;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

function handleSearch() {
    const input = document.getElementById("package-search").value.trim().toLowerCase();
    const cards = document.querySelectorAll(".package-card");
    let visibleCount = 0;
    cards.forEach(card => {
        const name = card.querySelector(".package-name").textContent.toLowerCase();
        if (name.includes(input)) {
            card.style.display = "";
            visibleCount++;
        } else {
            card.style.display = "none";
        }
    });
    const noPackagesMsg = document.getElementById("noPackagesFound");
    if (noPackagesMsg) {
        noPackagesMsg.style.display = visibleCount === 0 ? "block" : "none";
    }
}

document.getElementById("package-search").addEventListener("input", debounce(handleSearch, 300));