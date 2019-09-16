window.onload = function() {
    var container = document.getElementById('canvas-container');
    var w = container.offsetWidth;
    var h = container.offsetHeight;
    var canvas = container.appendChild(document.createElement('canvas'));
    canvas.width = w;
    canvas.height = h;
    canvas.style.backgroundImage = 'url("./imgs/bacheca/bkg.jpg")';
    canvas.style.backgroundSize = 'cover';
    canvas.style.backgroundPosition = 'center';
    canvas.style.backgroundRepeat = 'no-repeat';

    //draw(canvas.getContext('2d'), w, h);

    /**
     * 
     * @param {CanvasRenderingContext2D} ctx 
     * @param {number} w 
     * @param {number} h 
     */
    function draw(ctx, w, h) {
        ctx.save();
        ctx.fillStyle = 'red';
        ctx.fillRect(0, 0, w, h);
        ctx.fillStyle = 'black';
        ctx.fillRect(w / 4, h / 4, w / 2, h / 2);
    }

    function getCtx(canvas) {
        var ctx = {
            canvas: canvas,
            ctx: canvas.getContext("2d"),
            w: canvas.width,
            h: canvas.height,
            save: function() {
                this.ctx.save();
            },
            restore: function() {
                this.ctx.restore();
            },
            clear: function() {
                this.ctx.clearRect(0, 0, this.w, this.h);
            },
            fill: function() {
                this.ctx.fill();
            },
            stroke: function() {
                this.ctx.stroke();
            },
            clip: function() {
                this.ctx.clip();
            },
            transform: function(x, y, d, rotate) {
                this.ctx.transform(d, 0, 0, d, x, y);
                this.ctx.rotate(rotate);
            },
            clearArc: function(x, y, r, from, to) {
                this.save();
                this.ctx.beginPath();
                this.ctx.moveTo(x, y);
                this.ctx.arc(x, y, r, from, to);
                this.ctx.lineTo(x, y);
                this.ctx.clip();
                this.clear();
                this.restore();
            },
            drawRainbow: function(x, y, w, h) {
                this.ctx.save();
                this.ctx.setTransform(w, 0, 0, h, x, y);
                this.drawRainBall(0.1);
                this.ctx.restore();
            },
            drawRainBall: function(w) {
                var l = w ? 1 - w : 0.5;
                this.save();
                this.ctx.beginPath();
                this.ctx.arc(0, 0, 1, 0, 2 * Math.PI);
                var grd = this.ctx.createRadialGradient(0, 0, l, 0, 0, 1);
                grd.addColorStop(0, '#ff0000');
                grd.addColorStop(0.23, '#ffff00');
                grd.addColorStop(0.33, '#00ff00');
                grd.addColorStop(0.45, '#00ffff');
                grd.addColorStop(0.67, '#0000ff');
                grd.addColorStop(0.80, '#ff00ff');
                grd.addColorStop(1, '#ff0000');
                this.ctx.fillStyle = grd;
                this.ctx.fill();
                this.ctx.beginPath();
                this.ctx.arc(0, 0, l, 0, 2 * Math.PI);
                this.ctx.clip();
                this.ctx.clearRect(-1, -1, 2, 2);
                this.restore();
            },

        };
        return ctx;
    }
};
/*(function() {
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
})();*/