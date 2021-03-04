(function () {
    /**
     * @param {string} name
     * @return {string}
     */
    function getTasterSelector(name) {
        return `[data-tsr-url][data-tsr-event*="${name}"]:not([data-tsr-disabled])`;
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
     * @param {string} jsEvent Name of the standard JavaScript event that should be listened
     * @param {?string} tsrEvent Leave null if the name of the event matches the standards JavaScript event
     * @returns void
     */
    function registerTsrEvent(jsEvent, tsrEvent = null) {
        // in case taster uses the same naming
        if (!tsrEvent) {
            tsrEvent = jsEvent;
        }

        document.addEventListener(jsEvent, function (event) {
            const { target } = event;

            // checking if current element matches selector
            if (target instanceof Element && target.matches(getTasterSelector(tsrEvent))) {
                // disabling events that have to be fired once
                if (target.hasAttribute('data-tsr-once')) {
                    target.setAttribute('data-tsr-disabled', 'true');
                }

                // notifying server
                sendRequest(target);
            }
        });
    }

    // waiting for the dom to load
    document.addEventListener('DOMContentLoaded', function () {
        // attaching simple events
        registerTsrEvent('mouseenter', 'hover');
        registerTsrEvent('click');


        // Create an intersection observer with default options
        const viewObserver = new IntersectionObserver((entries, observer) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    // we only want to know if the element was visible once
                    observer.unobserve(entry.target);
                    sendRequest(entry.target);
                }
            }
        }, {
            // tells the observer to fire when half of the element is visible
            threshold: [.5]
        });

        // Use that IntersectionObserver to observe the visibility
        for (const element of document.querySelectorAll(getTasterSelector('view'))) {
            viewObserver.observe(element);
        }
    });
})()
