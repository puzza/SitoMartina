(function() {
    var selectedCard = null;
    var cards = document.getElementsByClassName('card');
    for (var i = 0; i < cards.length; i++) {
        var card = cards[i];
        card.addEventListener('transitionend', function(ev) {
            if (ev.currentTarget === selectedCard) {
                scrollTo(selectedCard);
            }
        });
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
        };
    }

    function scrollTo(el) {
        window.scrollTo({
            left: 0,
            top: el.getBoundingClientRect().top + el.ownerDocument.defaultView.pageYOffset,
            behavior: 'smooth'
        });
    }
})();