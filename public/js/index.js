"use strict";!function(){function t(t,e){e=e||window;for(var o=0;o<this.length;o++)t.call(e,this[o],o,this)}NodeList.prototype.forEach||(NodeList.prototype.forEach=t),Array.prototype.forEach||(Array.prototype.forEach=t)}(),Element.prototype.matches||(Element.prototype.matches=Element.prototype.matchesSelector||Element.prototype.webkitMatchesSelector||Element.prototype.khtmlMatchesSelector||Element.prototype.mozMatchesSelector||Element.prototype.msMatchesSelector||Element.prototype.oMatchesSelector||function(t){for(var e=document.querySelectorAll(t),o=e.length;0<=--o&&e[o]!==this;);return-1<o}),function(){function o(t){return'[data-tsr-url][data-tsr-event="'.concat(t,'"]:not([data-tsr-disabled])')}function n(t){var e=new XMLHttpRequest;e.open("POST",t.getAttribute("data-tsr-url")),e.send()}["mouseover","click"].forEach(function(e){document.addEventListener(e,function(t){t=t.target;t instanceof Element&&t.matches(o(e))&&(t.hasAttribute("data-tsr-once")&&t.setAttribute("data-tsr-disabled","true"),n(t))})}),window.addEventListener("scroll",function(){document.querySelectorAll(o("view")).forEach(function(t){var e;0<=(e=(e=t).getBoundingClientRect()).top&&0<=e.left&&e.bottom<=(window.innerHeight||document.documentElement.clientHeight)&&e.right<=(window.innerWidth||document.documentElement.clientWidth)&&(t.setAttribute("data-tsr-disabled","true"),n(t))})})}();