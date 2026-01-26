const sortSelect = document.getElementById("sortSelect");
const itemsContainer = document.querySelector(".items-container");

sortSelect.addEventListener("change", function () {
  const value = this.value;
  let cards = Array.from(document.querySelectorAll(".product-card"));

  cards = cards.filter((card) => !card.classList.contains("hidden"));

  cards.sort((a, b) => {
    const priceA = parseFloat(
      a.querySelector(".price").innerText.replace("$", ""),
    );
    const priceB = parseFloat(
      b.querySelector(".price").innerText.replace("$", ""),
    );
    const idA = parseInt(a.getAttribute("data-id"));
    const idB = parseInt(b.getAttribute("data-id"));

    switch (value) {
      case "price-asc":
        return priceA - priceB;
      case "price-desc":
        return priceB - priceA;
      case "latest":
        return idB - idA;
      case "oldest":
        return idA - idB;
      default:
        return 0;
    }
  });

  cards.forEach((card) => itemsContainer.appendChild(card));
});
