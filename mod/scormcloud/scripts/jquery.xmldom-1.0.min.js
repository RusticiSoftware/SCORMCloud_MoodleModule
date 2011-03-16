/*
 * jQuery xmlDOM Plugin v1.0
 * http://outwestmedia.com/jquery-plugins/xmldom/
 *
 * Released: 2009-04-06
 * Version: 1.0
 *
 * Copyright (c) 2009 Jonathan Sharp, Out West Media LLC.
 * Dual licensed under the MIT and GPL licenses.
 * http://docs.jquery.com/License
 */
(function(a){if(window.DOMParser==undefined&&window.ActiveXObject){DOMParser=function(){};DOMParser.prototype.parseFromString=function(c){var b=new ActiveXObject("Microsoft.XMLDOM");b.async="false";b.loadXML(c);return b}}a.xmlDOM=function(b,h){try{var d=(new DOMParser()).parseFromString(b,"text/xml");if(a.isXMLDoc(d)){var c=a("parsererror",d);if(c.length==1){throw ("Error: "+a(d).text())}}else{throw ("Unable to parse XML")}}catch(f){var g=(f.name==undefined?f:f.name+": "+f.message);if(a.isFunction(h)){h(g)}else{a(document).trigger("xmlParseError",[g])}return a([])}return a(d)}})(jQuery);