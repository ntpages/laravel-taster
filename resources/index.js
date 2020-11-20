(function () {
    /**
     * @param {string} name
     * @return {string}
     */
    function getTasterSelector(name) {
        return `[data-tsr-url][data-tsr-event="${name}"]:not([data-tsr-disabled])`;
    }

    /**
     * Notifies the server that the actions has happened
     * @param {Element} target
     */
    function sendRequest(target) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', target.getAttribute('data-tsr-url'))
        xhr.send();
    }

    /**
     * @param {Element} element
     * @return {boolean}
     */
    function isVisible(element) {
        const r = element.getBoundingClientRect();
        return (
            r.top >= 0 &&
            r.left >= 0 &&
            r.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            r.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }


// initializing default callbacks
    ['mouseover', 'click'].forEach(function (name) {
        // delegating simple events
        document.addEventListener(name, function ({ target }) {
            // catching our guy
            if (target instanceof Element && target.matches(getTasterSelector(name))) {
                // disabling events that have to be fired once
                if (target.hasAttribute('data-tsr-once')) {
                    target.setAttribute('data-tsr-disabled', 'true');
                }
                sendRequest(target);
            }
        });
    });

// listening scroll event
    window.addEventListener('scroll', function () {
        // elements that collect impressions
        document.querySelectorAll(getTasterSelector('view'))
            // only executed once
            .forEach(function (element) {
                if (isVisible(element)) {
                    element.setAttribute('data-tsr-disabled', 'true');
                    sendRequest(element);
                }
            });
    })
})()
