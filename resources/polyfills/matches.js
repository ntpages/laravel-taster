if (!Element.prototype.matches) {
    Element.prototype.matches =
        // checking default implementations
        Element.prototype['matchesSelector'] ||
        Element.prototype['webkitMatchesSelector'] ||
        Element.prototype['khtmlMatchesSelector'] ||
        Element.prototype['mozMatchesSelector'] ||
        Element.prototype['msMatchesSelector'] ||
        Element.prototype['oMatchesSelector'] ||
        /**
         * @param {string} selector
         * @return {boolean}
         */
        function matches(selector) {
            const matches = document.querySelectorAll(selector);
            let i = matches.length;

            while (--i >= 0 && matches[i] !== this) {}

            return i > -1;
        }
}
