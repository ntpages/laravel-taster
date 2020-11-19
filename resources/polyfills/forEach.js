(function () {
    /**
     * @param {function} callback
     * @param thisArg
     */
    function forEach(callback, thisArg) {
        thisArg = thisArg || window;

        for (let i = 0; i < this.length; i++) {
            callback.call(thisArg, this[i], i, this);
        }
    }

    if (!NodeList.prototype.forEach) {
        NodeList.prototype.forEach = forEach;
    }

    if (!Array.prototype.forEach) {
        Array.prototype.forEach = forEach;
    }
})()
