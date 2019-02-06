/*!
* ZeroClipboard
* The ZeroClipboard library provides an easy way to copy text to the clipboard using an invisible Adobe Flash movie and a JavaScript interface.
* Copyright (c) 2013 Jon Rohan, James M. Greene
* Licensed MIT
* http://zeroclipboard.org/
* v1.2.3
*/
(function() {
  "use strict";
  var currentElement;
  var gluedElements = [];
  var flashState = {
    global: {
      noflash: null,
      wrongflash: null,
      version: "0.0.0"
    },
    clients: {}
  };
  var _camelizeCssPropName = function() {
    var matcherRegex = /\-([a-z])/g, replacerFn = function(match, group) {
      return group.toUpperCase();
    };
    return function(prop) {
      return prop.replace(matcherRegex, replacerFn);
    };
  }();
  var _getStyle = function(el, prop) {
    var value, camelProp, tagName, possiblePointers, i, len;
    if (window.getComputedStyle) {
      value = window.getComputedStyle(el, null).getPropertyValue(prop);
    } else {
      camelProp = _camelizeCssPropName(prop);
      if (el.currentStyle) {
        value = el.currentStyle[camelProp];
      } else {
        value = el.style[camelProp];
      }
    }
    if (prop === "cursor") {
      if (!value || value === "auto") {
        tagName = el.tagName.toLowerCase();
        possiblePointers = [ "a" ];
        for (i = 0, len = possiblePointers.length; i < len; i++) {
          if (tagName === possiblePointers[i]) {
            return "pointer";
          }
        }
      }
    }
    return value;
  };
  var _elementMouseOver = function(event) {
    if (!ZeroClipboard.prototype._singleton) return;
    if (!event) {
      event = window.event;
    }
    var target;
    if (this !== window) {
      target = this;
    } else if (event.target) {
      target = event.target;
    } else if (event.srcElement) {
      target = event.srcElement;
    }
    ZeroClipboard.prototype._singleton.setCurrent(target);
  };
  var _addEventHandler = function(element, method, func) {
    if (element.addEventListener) {
      element.addEventListener(method, func, false);
    } else if (element.attachEvent) {
      element.attachEvent("on" + method, func);
    }
  };
  var _removeEventHandler = function(element, method, func) {
    if (element.removeEventListener) {
      element.removeEventListener(method, func, false);
    } else if (element.detachEvent) {
      element.detachEvent("on" + method, func);
    }
  };
  var _addClass = function(element, value) {
    if (element.addClass) {
      element.addClass(value);
      return element;
    }
    if (value && typeof value === "string") {
      var classNames = (value || "").split(/\s+/);
      if (element.nodeType === 1) {
        if (!element.className) {
          element.className = value;
        } else {
          var className = " " + element.className + " ", setClass = element.className;
          for (var c = 0, cl = classNames.length; c < cl; c++) {
            if (className.indexOf(" " + classNames[c] + " ") < 0) {
              setClass += " " + classNames[c];
            }
          }
          element.className = setClass.replace(/^\s+|\s+$/g, "");
        }
      }
    }
    return element;
  };
  var _removeClass = function(element, value) {
    if (element.removeClass) {
      element.removeClass(value);
      return element;
    }
    if (value && typeof value === "string" || value === undefined) {
      var classNames = (value || "").split(/\s+/);
      if (element.nodeType === 1 && element.className) {
        if (value) {
          var className = (" " + element.className + " ").replace(/[\n\t]/g, " ");
          for (var c = 0, cl = classNames.length; c < cl; c++) {
            className = className.replace(" " + classNames[c] + " ", " ");
          }
          element.className = className.replace(/^\s+|\s+$/g, "");
        } else {
          element.className = "";
        }
      }
    }
    return element;
  };
  var _getZoomFactor = function() {
    var rect, physicalWidth, logicalWidth, zoomFactor = 1;
    if (typeof document.body.getBoundingClientRect === "function") {
      rect = document.body.getBoundingClientRect();
      physicalWidth = rect.right - rect.left;
      logicalWidth = document.body.offsetWidth;
      zoomFactor = Math.round(physicalWidth / logicalWidth * 100) / 100;
    }
    return zoomFactor;
  };
  var _getDOMObjectPosition = function(obj, defaultZIndex) {
    var info = {
      left: 0,
      top: 0,
      width: 0,
      height: 0,
      zIndex: _getSafeZIndex(defaultZIndex) - 1
    };
    if (obj.getBoundingClientRect) {
      var rect = obj.getBoundingClientRect();
      var pageXOffset, pageYOffset, zoomFactor;
      if ("pageXOffset" in window && "pageYOffset" in window) {
        pageXOffset = window.pageXOffset;
        pageYOffset = window.pageYOffset;
      } else {
        zoomFactor = _getZoomFactor();
        pageXOffset = Math.round(document.documentElement.scrollLeft / zoomFactor);
        pageYOffset = Math.round(document.documentElement.scrollTop / zoomFactor);
      }
      var leftBorderWidth = document.documentElement.clientLeft || 0;
      var topBorderWidth = document.documentElement.clientTop || 0;
      info.left = rect.left + pageXOffset - leftBorderWidth;
      info.top = rect.top + pageYOffset - topBorderWidth;
      info.width = "width" in rect ? rect.width : rect.right - rect.left;
      info.height = "height" in rect ? rect.height : rect.bottom - rect.top;
    }
    return info;
  };
  var _noCache = function(path, options) {
    var useNoCache = !(options && options.useNoCache === false);
    if (useNoCache) {
      return (path.indexOf("?") === -1 ? "?" : "&") + "nocache=" + new Date().getTime();
    } else {
      return "";
    }
  };
  var _vars = function(options) {
    var str = [];
    var origins = [];
    if (options.trustedOrigins) {
      if (typeof options.trustedOrigins === "string") {
        origins.push(options.trustedOrigins);
      } else if (typeof options.trustedOrigins === "object" && "length" in options.trustedOrigins) {
        origins = origins.concat(options.trustedOrigins);
      }
    }
    if (options.trustedDomains) {
      if (typeof options.trustedDomains === "string") {
        origins.push(options.trustedDomains);
      } else if (typeof options.trustedDomains === "object" && "length" in options.trustedDomains) {
        origins = origins.concat(options.trustedDomains);
      }
    }
    if (origins.length) {
      str.push("trustedOrigins=" + encodeURIComponent(origins.join(",")));
    }
    if (typeof options.amdModuleId === "string" && options.amdModuleId) {
      str.push("amdModuleId=" + encodeURIComponent(options.amdModuleId));
    }
    if (typeof options.cjsModuleId === "string" && options.cjsModuleId) {
      str.push("cjsModuleId=" + encodeURIComponent(options.cjsModuleId));
    }
    return str.join("&");
  };
  var _inArray = function(elem, array) {
    if (array.indexOf) {
      return array.indexOf(elem);
    }
    for (var i = 0, length = array.length; i < length; i++) {
      if (array[i] === elem) {
        return i;
      }
    }
    return -1;
  };
  var _prepGlue = function(elements) {
    if (typeof elements === "string") throw new TypeError("ZeroClipboard doesn't accept query strings.");
    if (!elements.length) return [ elements ];
    return elements;
  };
  var _dispatchCallback = function(func, element, instance, args, async) {
    if (async) {
      window.setTimeout(function() {
        func.call(element, instance, args);
      }, 0);
    } else {
      func.call(element, instance, args);
    }
  };
  var _getSafeZIndex = function(val) {
    var zIndex, tmp;
    if (val) {
      if (typeof val === "number" && val > 0) {
        zIndex = val;
      } else if (typeof val === "string" && (tmp = parseInt(val, 10)) && !isNaN(tmp) && tmp > 0) {
        zIndex = tmp;
      }
    }
    if (!zIndex) {
      if (typeof _defaults.zIndex === "number" && _defaults.zIndex > 0) {
        zIndex = _defaults.zIndex;
      } else if (typeof _defaults.zIndex === "string" && (tmp = parseInt(_defaults.zIndex, 10)) && !isNaN(tmp) && tmp > 0) {
        zIndex = tmp;
      }
    }
    return zIndex || 0;
  };
  var _deprecationWarning = function(deprecatedApiName, debugEnabled) {
    if (deprecatedApiName && debugEnabled !== false && typeof console !== "undefined" && console && (console.warn || console.log)) {
      var deprecationWarning = "`" + deprecatedApiName + "` is deprecated. See docs for more info:\n" + "    https://github.com/zeroclipboard/zeroclipboard/blob/master/docs/instructions.md#deprecations";
      if (console.warn) {
        console.warn(deprecationWarning);
      } else {
        console.log(deprecationWarning);
      }
    }
  };
  var ZeroClipboard = function(elements, options) {
    if (elements) (ZeroClipboard.prototype._singleton || this).glue(elements);
    if (ZeroClipboard.prototype._singleton) return ZeroClipboard.prototype._singleton;
    ZeroClipboard.prototype._singleton = this;
    this.options = {};
    for (var kd in _defaults) this.options[kd] = _defaults[kd];
    for (var ko in options) this.options[ko] = options[ko];
    this.handlers = {};
    if (typeof flashState.global.noflash !== "boolean") {
      flashState.global.noflash = !_detectFlashSupport();
    }
    if (!flashState.clients.hasOwnProperty(this.options.moviePath)) {
      flashState.clients[this.options.moviePath] = {
        ready: false
      };
    }
    if (flashState.global.noflash === false) {
      _bridge();
    }
  };
  ZeroClipboard.prototype.setCurrent = function(element) {
    currentElement = element;
    _reposition.call(this);
    var titleAttr = element.getAttribute("title");
    if (titleAttr) {
      this.setTitle(titleAttr);
    }
    var useHandCursor = this.options.forceHandCursor === true || _getStyle(element, "cursor") === "pointer";
    _setHandCursor.call(this, useHandCursor);
    return this;
  };
  ZeroClipboard.prototype.setText = function(newText) {
    if (newText && newText !== "") {
      this.options.text = newText;
      if (this.ready()) this.flashBridge.setText(newText);
    }
    return this;
  };
  ZeroClipboard.prototype.setTitle = function(newTitle) {
    if (newTitle && newTitle !== "") this.htmlBridge.setAttribute("title", newTitle);
    return this;
  };
  ZeroClipboard.prototype.setSize = function(width, height) {
    if (this.ready()) this.flashBridge.setSize(width, height);
    return this;
  };
  var _setHandCursor = function(enabled) {
    if (this.ready()) this.flashBridge.setHandCursor(enabled);
  };
  var _detectFlashSupport = function() {
    var hasFlash = false;
    if (typeof flashState.global.noflash === "boolean") {
      hasFlash = flashState.global.noflash === false;
    } else {
      if (typeof ActiveXObject === "function") {
        try {
          if (new ActiveXObject("ShockwaveFlash.ShockwaveFlash")) {
            hasFlash = true;
          }
        } catch (error) {}
      }
      if (!hasFlash && navigator.mimeTypes["application/x-shockwave-flash"]) {
        hasFlash = true;
      }
    }
    return hasFlash;
  };
  ZeroClipboard.version = "1.2.3";
  var _defaults = {
    moviePath: "ZeroClipboard.swf",
    trustedOrigins: null,
    text: null,
    hoverClass: "zeroclipboard-is-hover",
    activeClass: "zeroclipboard-is-active",
    allowScriptAccess: "sameDomain",
    useNoCache: true,
    forceHandCursor: false,
    zIndex: 999999999,
    debug: true
  };
  ZeroClipboard.setDefaults = function(options) {
    for (var ko in options) _defaults[ko] = options[ko];
  };
  ZeroClipboard.destroy = function() {
    if (ZeroClipboard.prototype._singleton) {
      ZeroClipboard.prototype._singleton.unglue(gluedElements);
      var bridge = ZeroClipboard.prototype._singleton.htmlBridge;
      if (bridge && bridge.parentNode) {
        bridge.parentNode.removeChild(bridge);
      }
      delete ZeroClipboard.prototype._singleton;
    }
  };
  var _amdModuleId = null;
  var _cjsModuleId = null;
  var _bridge = function() {
    var flashBridge, len;
    var client = ZeroClipboard.prototype._singleton;
    var container = document.getElementById("global-zeroclipboard-html-bridge");
    if (!container) {
      var opts = {};
      for (var ko in client.options) opts[ko] = client.options[ko];
      opts.amdModuleId = _amdModuleId;
      opts.cjsModuleId = _cjsModuleId;
      var flashvars = _vars(opts);
      var html = '      <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" id="global-zeroclipboard-flash-bridge" width="100%" height="100%">         <param name="movie" value="' + client.options.moviePath + _noCache(client.options.moviePath, client.options) + '"/>         <param name="allowScriptAccess" value="' + client.options.allowScriptAccess + '"/>         <param name="scale" value="exactfit"/>         <param name="loop" value="false"/>         <param name="menu" value="false"/>         <param name="quality" value="best" />         <param name="bgcolor" value="#ffffff"/>         <param name="wmode" value="transparent"/>         <param name="flashvars" value="' + flashvars + '"/>         <embed src="' + client.options.moviePath + _noCache(client.options.moviePath, client.options) + '"           loop="false" menu="false"           quality="best" bgcolor="#ffffff"           width="100%" height="100%"           name="global-zeroclipboard-flash-bridge"           allowScriptAccess="always"           allowFullScreen="false"           type="application/x-shockwave-flash"           wmode="transparent"           pluginspage="http://www.macromedia.com/go/getflashplayer"           flashvars="' + flashvars + '"           scale="exactfit">         </embed>       </object>';
      container = document.createElement("div");
      container.id = "global-zeroclipboard-html-bridge";
      container.setAttribute("class", "global-zeroclipboard-container");
      container.style.position = "absolute";
      container.style.left = "0px";
      container.style.top = "-9999px";
      container.style.width = "15px";
      container.style.height = "15px";
      container.style.zIndex = "" + _getSafeZIndex(client.options.zIndex);
      document.body.appendChild(container);
      container.innerHTML = html;
    }
    client.htmlBridge = container;
    flashBridge = document["global-zeroclipboard-flash-bridge"];
    if (flashBridge && (len = flashBridge.length)) {
      flashBridge = flashBridge[len - 1];
    }
    client.flashBridge = flashBridge || container.children[0].lastElementChild;
  };
  ZeroClipboard.prototype.resetBridge = function() {
    if (this.htmlBridge) {
      this.htmlBridge.style.left = "0px";
      this.htmlBridge.style.top = "-9999px";
      this.htmlBridge.removeAttribute("title");
    }
    if (currentElement) {
      _removeClass(currentElement, this.options.activeClass);
      currentElement = null;
    }
    this.options.text = null;
    return this;
  };
  ZeroClipboard.prototype.ready = function() {
    return flashState.clients[this.options.moviePath].ready === true;
  };
  var _reposition = function() {
    if (currentElement) {
      var pos = _getDOMObjectPosition(currentElement, this.options.zIndex);
      this.htmlBridge.style.top = pos.top + "px";
      this.htmlBridge.style.left = pos.left + "px";
      this.htmlBridge.style.width = pos.width + "px";
      this.htmlBridge.style.height = pos.height + "px";
      this.htmlBridge.style.zIndex = pos.zIndex + 1;
      this.setSize(pos.width, pos.height);
    }
    return this;
  };
  ZeroClipboard.dispatch = function(eventName, args) {
    if (typeof eventName === "string" && eventName) {
      var client = ZeroClipboard.prototype._singleton;
      var cleanEventName = eventName.toLowerCase().replace(/^on/, "");
      if (cleanEventName) {
        _receiveEvent.call(client, cleanEventName, args);
      }
    }
  };
  ZeroClipboard.prototype.on = function(eventName, func) {
    var events = eventName.toString().split(/\s/g), added = {};
    for (var i = 0, len = events.length; i < len; i++) {
      eventName = events[i].toLowerCase().replace(/^on/, "");
      added[eventName] = true;
      if (!this.handlers[eventName]) {
        this.handlers[eventName] = func;
      }
    }
    if (added.noflash && flashState.global.noflash) {
      _receiveEvent.call(this, "onNoFlash", {});
    }
    if (added.wrongflash && flashState.global.wrongflash) {
      _receiveEvent.call(this, "onWrongFlash", {
        flashVersion: flashState.global.version
      });
    }
    if (added.load && flashState.clients[this.options.moviePath].ready) {
      _receiveEvent.call(this, "onLoad", {
        flashVersion: flashState.global.version
      });
    }
    return this;
  };
  ZeroClipboard.prototype.addEventListener = ZeroClipboard.prototype.on;
  ZeroClipboard.prototype.off = function(eventName, func) {
    var events = eventName.toString().split(/\s/g);
    for (var i = 0; i < events.length; i++) {
      eventName = events[i].toLowerCase().replace(/^on/, "");
      for (var event in this.handlers) {
        if (event === eventName && this.handlers[event] === func) {
          delete this.handlers[event];
        }
      }
    }
    return this;
  };
  ZeroClipboard.prototype.removeEventListener = ZeroClipboard.prototype.off;
  var _receiveEvent = function(eventName, args) {
    eventName = eventName.toString().toLowerCase().replace(/^on/, "");
    var element = currentElement;
    var performCallbackAsync = true;
    switch (eventName) {
     case "load":
      if (args && args.flashVersion) {
        if (!_isFlashVersionSupported(args.flashVersion)) {
          _receiveEvent.call(this, "onWrongFlash", {
            flashVersion: args.flashVersion
          });
          return;
        }
        flashState.clients[this.options.moviePath].ready = true;
        flashState.global.version = args.flashVersion;
      }
      break;

     case "wrongflash":
      if (args && args.flashVersion && !_isFlashVersionSupported(args.flashVersion)) {
        flashState.global.wrongflash = true;
        flashState.global.version = args.flashVersion;
      }
      break;

     case "mouseover":
      _addClass(element, this.options.hoverClass);
      break;

     case "mouseout":
      _removeClass(element, this.options.hoverClass);
      this.resetBridge();
      break;

     case "mousedown":
      _addClass(element, this.options.activeClass);
      break;

     case "mouseup":
      _removeClass(element, this.options.activeClass);
      break;

     case "datarequested":
      var targetId = element.getAttribute("data-clipboard-target"), targetEl = !targetId ? null : document.getElementById(targetId);
      if (targetEl) {
        var textContent = targetEl.value || targetEl.textContent || targetEl.innerText;
        if (textContent) {
          this.setText(textContent);
        }
      } else {
        var defaultText = element.getAttribute("data-clipboard-text");
        if (defaultText) {
          this.setText(defaultText);
        }
      }
      performCallbackAsync = false;
      break;

     case "complete":
      this.options.text = null;
      break;
    }
    if (this.handlers[eventName]) {
      var func = this.handlers[eventName];
      if (typeof func === "string" && typeof window[func] === "function") {
        func = window[func];
      }
      if (typeof func === "function") {
        _dispatchCallback(func, element, this, args, performCallbackAsync);
      }
    }
  };
  ZeroClipboard.prototype.glue = function(elements) {
    elements = _prepGlue(elements);
    for (var i = 0; i < elements.length; i++) {
      if (elements[i] && elements[i].nodeType === 1) {
        if (_inArray(elements[i], gluedElements) == -1) {
          gluedElements.push(elements[i]);
          _addEventHandler(elements[i], "mouseover", _elementMouseOver);
        }
      }
    }
    return this;
  };
  ZeroClipboard.prototype.unglue = function(elements) {
    elements = _prepGlue(elements);
    for (var i = 0; i < elements.length; i++) {
      _removeEventHandler(elements[i], "mouseover", _elementMouseOver);
      var arrayIndex = _inArray(elements[i], gluedElements);
      if (arrayIndex != -1) gluedElements.splice(arrayIndex, 1);
    }
    return this;
  };
  function _isFlashVersionSupported(flashVersion) {
    return parseFloat(flashVersion.replace(/,/g, ".").replace(/[^0-9\.]/g, "")) >= 10;
  }
  ZeroClipboard.detectFlashSupport = function() {
    var debugEnabled = ZeroClipboard.prototype._singleton && ZeroClipboard.prototype._singleton.options.debug || _defaults.debug;
    _deprecationWarning("ZeroClipboard.detectFlashSupport", debugEnabled);
    return _detectFlashSupport();
  };
  ZeroClipboard.prototype.setHandCursor = function(enabled) {
    _deprecationWarning("ZeroClipboard.prototype.setHandCursor", this.options.debug);
    enabled = typeof enabled === "boolean" ? enabled : !!enabled;
    _setHandCursor.call(this, enabled);
    this.options.forceHandCursor = enabled;
    return this;
  };
  ZeroClipboard.prototype.reposition = function() {
    _deprecationWarning("ZeroClipboard.prototype.reposition", this.options.debug);
    return _reposition.call(this);
  };
  ZeroClipboard.prototype.receiveEvent = function(eventName, args) {
    _deprecationWarning("ZeroClipboard.prototype.receiveEvent", this.options.debug);
    if (typeof eventName === "string" && eventName) {
      var cleanEventName = eventName.toLowerCase().replace(/^on/, "");
      if (cleanEventName) {
        _receiveEvent.call(this, cleanEventName, args);
      }
    }
  };
  if (typeof define === "function" && define.amd) {
    define([ "require", "exports", "module" ], function(require, exports, module) {
      _amdModuleId = module && module.id || null;
      return ZeroClipboard;
    });
  } else if (typeof module === "object" && module && typeof module.exports === "object" && module.exports) {
    _cjsModuleId = module.id || null;
    module.exports = ZeroClipboard;
  } else {
    window.ZeroClipboard = ZeroClipboard;
  }
})();

/*
 * Copyright 2010 akquinet
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 *  This JQuery Plugin will help you in showing some nice Toast-Message like notification messages. The behavior is
 *  similar to the android Toast class.
 *  You have 4 different toast types you can show. Each type comes with its own icon and colored border. The types are:
 *  - notice
 *  - success
 *  - warning
 *  - error
 *
 *  The following methods will display a toast message:
 *
 *   $().toastmessage('showNoticeToast', 'some message here');
 *   $().toastmessage('showSuccessToast', "some message here");
 *   $().toastmessage('showWarningToast', "some message here");
 *   $().toastmessage('showErrorToast', "some message here");
 *
 *   // user configured toastmessage:
 *   $().toastmessage('showToast', {
 *      text     : 'Hello World',
 *      sticky   : true,
 *      position : 'top-right',
 *      type     : 'success',
 *      close    : function () {console.log("toast is closed ...");}
 *   });
 *
 *   To see some more examples please have a look into the Tests in src/test/javascript/ToastmessageTest.js
 *
 *   For further style configuration please see corresponding css file: jquery-toastmessage.css
 *
 *   This plugin is based on the jquery-notice (http://sandbox.timbenniks.com/projects/jquery-notice/)
 *   but is enhanced in several ways:
 *
 *   configurable positioning
 *   convenience methods for different message types
 *   callback functionality when closing the toast
 *   included some nice free icons
 *   reimplemented to follow jquery plugin good practices rules
 *
 *   Author: Daniel Bremer-Tonn
**/

(function($)
{
	var settings = {
				inEffect: 			{opacity: 'show'},	// in effect
				inEffectDuration: 	600,				// in effect duration in miliseconds
				stayTime: 			3000,				// time in miliseconds before the item has to disappear
				text: 				'',					// content of the item. Might be a string or a jQuery object. Be aware that any jQuery object which is acting as a message will be deleted when the toast is fading away.
				sticky: 			false,				// should the toast item sticky or not?
				type: 				'notice', 			// notice, warning, error, success
                position:           'top-right',        // top-left, top-center, top-right, middle-left, middle-center, middle-right ... Position of the toast container holding different toast. Position can be set only once at the very first call, changing the position after the first call does nothing
                closeText:          '',                 // text which will be shown as close button, set to '' when you want to introduce an image via css
                close:              null                // callback function when the toastmessage is closed
            };

    var methods = {
        init : function(options)
		{
			if (options) {
                $.extend( settings, options );
            }
		},

        showToast : function(options)
		{
			var localSettings = {};
            $.extend(localSettings, settings, options);

			// declare variables
            var toastWrapAll, toastItemOuter, toastItemInner, toastItemClose, toastItemImage;

			toastWrapAll	= (!$('.toast-container').length) ? $('<div></div>').addClass('toast-container').addClass('toast-position-' + localSettings.position).appendTo('body') : $('.toast-container');
			toastItemOuter	= $('<div></div>').addClass('toast-item-wrapper');
			toastItemInner	= $('<div></div>').hide().addClass('toast-item toast-type-' + localSettings.type).appendTo(toastWrapAll).html($('<p>').append (localSettings.text)).animate(localSettings.inEffect, localSettings.inEffectDuration).wrap(toastItemOuter);
			toastItemClose	= $('<div></div>').addClass('toast-item-close').prependTo(toastItemInner).html(localSettings.closeText).click(function() { $().toastmessage('removeToast',toastItemInner, localSettings) });
			toastItemImage  = $('<div></div>').addClass('toast-item-image').addClass('toast-item-image-' + localSettings.type).prependTo(toastItemInner);

            if(navigator.userAgent.match(/MSIE 6/i))
			{
		    	toastWrapAll.css({top: document.documentElement.scrollTop});
		    }

			if(!localSettings.sticky)
			{
				setTimeout(function()
				{
					$().toastmessage('removeToast', toastItemInner, localSettings);
				},
				localSettings.stayTime);
			}
            return toastItemInner;
		},

        showNoticeToast : function (message)
        {
            var options = {text : message, type : 'notice'};
            return $().toastmessage('showToast', options);
        },

        showSuccessToast : function (message)
        {
            var options = {text : message, type : 'success'};
            return $().toastmessage('showToast', options);
        },

        showErrorToast : function (message)
        {
            var options = {text : message, type : 'error'};
            return $().toastmessage('showToast', options);
        },

        showWarningToast : function (message)
        {
            var options = {text : message, type : 'warning'};
            return $().toastmessage('showToast', options);
        },

		removeToast: function(obj, options)
		{
			obj.animate({opacity: '0'}, 600, function()
			{
				obj.parent().animate({height: '0px'}, 300, function()
				{
					obj.parent().remove();
				});
			});
            // callback
            if (options && options.close !== null)
            {
                options.close();
            }
		}
	};

    $.fn.toastmessage = function( method ) {

        // Method calling logic
        if ( methods[method] ) {
          return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
          return methods.init.apply( this, arguments );
        } else {
          $.error( 'Method ' +  method + ' does not exist on jQuery.toastmessage' );
        }
    };
var flashPath = "/assets/ZeroClipboard.swf"
        function createClip(SourceTextElId)
        {
                var clip = new ZeroClipboard( document.getElementById(SourceTextElId), {
                         moviePath: flashPath
                } );
                clip.on( "load", function(client) {
                        client.on( "complete", function(client, args) {
                                console.log(SourceTextElId)
                                createClip(SourceTextElId)
														var msg = 'скопировано в буфер обмена';
		showme(msg);
		// $(".copyr").animate({opacity: "1"}, 300);
		// $(".copyr").animate({opacity: "0"}, 300);
                        } );
                })
        }
        $(function(){
                createClip('copyfund')
                createClip('copybill')
        })
})(jQuery);

function showme(data) {
    $().toastmessage('showToast', {
        text: data,
        sticky: false,
        position: 'top-right',
        type: 'success'
    });
}

$( document ).ready(function() {
    $('.tab-nav').delegate('li:not(.cur)', 'click', function() {
        $(this).addClass('cur').siblings().removeClass('cur').parents().find('.tab-box').hide().removeClass('cur').eq($(this).index()).addClass('cur').fadeIn(500);
    });
});


	