(function() {
    var selectedCard = null;
    var cards = document.getElementsByClassName('card');
    for (var i = 0; i < cards.length; i++) {
        var card = cards[i];
        card.onclick = function() {
            if (selectedCard) {
                selectedCard.classList.remove('open');
            }
            if (this === selectedCard) {
                selectedCard = null;
            } else {
                this.classList.add('open');
                selectedCard = this;
            }
        }
    }
})();