(function() {
    //test json
    var json = [{
        date: '03/02/1986',
        title: 'Sono nato',
        address: 'Moncalieri'
    }];

    var container = document.getElementById('container');
    for (var i = 0; i < json.length; i++) {
        var card = buildCard(json[i]);
        container.append(card);
    }

    function buildCard(json) {
        var domEl = document.createElement('div');
        if (json.img) {
            var img = document.createElement('img');
            img.src = json.img;
            domEl.append(img);
        }
        if (json.date) {
            var date = document.createElement('div');
            date.classList.add('date');
            date.textContent = json.date;
            domEl.append(date);
        }
        if (json.title) {
            var title = document.createElement('div');
            title.classList.add('title');
            title.textContent = json.title.toUpperCase();
            domEl.append(title);
        }
        if (json.address) {
            var address = document.createElement('div');
            address.classList.add('address');
            address.textContent = json.address;
            domEl.append(address);
        }
        return domEl;
    }
})();