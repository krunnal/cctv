/* vim: noet ts=4 sw=4
 * Version: 1.0
 * Date: 2010-01-28
 * Author: Romuald Brunet <romuald@chivil.com>
 */
(function ($) {
    $.fn.uncomment = function () {
        for (var i = 0, l = this.length; i < l; i++) {
            for (var j = 0, len = this[i].childNodes.length; j < len; j++) {
                if (this[i].childNodes[j].nodeType === 8) {
                    var content = this[i].childNodes[j].nodeValue;
                    $(this[i].childNodes[j]).replaceWith(content);
                }
            }
        }
    };
})(jQuery);
