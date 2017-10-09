// http://www.makeitgo.ws/articles/animationframe/
;(function() {
    var lastFrame, method, now, queue, requestAnimationFrame, timer, vendor, _i, _len, _ref, _ref1;
    method = 'native';
    now = Date.now || function() {
        return new Date().getTime();
    };
    requestAnimationFrame = window.requestAnimationFrame;
    _ref = ['webkit', 'moz', 'o', 'ms'];
    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        vendor = _ref[_i];
        if (requestAnimationFrame == null) {
            requestAnimationFrame = window[vendor + 'RequestAnimationFrame'];
        }
    }
    if (requestAnimationFrame == null) {
        method = 'timer';
        lastFrame = 0;
        queue = timer = null;
        requestAnimationFrame = function(callback) {
            var fire, nextFrame, time;
            if (queue != null) {
                queue.push(callback);
                return;
            }
            time = now();
            nextFrame = Math.max(0, 16.66 - (time - lastFrame));
            queue = [callback];
            lastFrame = time + nextFrame;
            fire = function() {
                var cb, q, _j, _len1;
                q = queue;
                queue = null;
                for (_j = 0, _len1 = q.length; _j < _len1; _j++) {
                    cb = q[_j];
                    cb(lastFrame);
                }
            };
            timer = setTimeout(fire, nextFrame);
        };
    }
    requestAnimationFrame(function(time) {
        var offset, _ref1;
        if (time < 1e12) {
            if (((_ref1 = window.performance) != null ? _ref1.now : void 0) != null) {
                requestAnimationFrame.now = function() {
                    return window.performance.now();
                };
                requestAnimationFrame.method = 'native-highres';
            } else {
                offset = now() - time;
                requestAnimationFrame.now = function() {
                    return now() - offset;
                };
                requestAnimationFrame.method = 'native-highres-noperf';
            }
        } else {
            requestAnimationFrame.now = now;
        }
    });
    requestAnimationFrame.now = ((_ref1 = window.performance) != null ? _ref1.now : void 0) != null ? (function() {
        return window.performance.now();
    }) : now;
    requestAnimationFrame.method = method;
    window.requestAnimationFrame = requestAnimationFrame;

    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function(timer) {
            clearTimeout(timer);
        };
})();


/**
 * Created by Eric Lee on 8/24/14.
 * Modified by Eric Lee on 6/17/16.
 */
;(function(){
    'use strict';

    var reqAnimate = function(element, options) {
        this.options = {
            fps: 30,
            totalFrames: 16,
            time: Infinity,
            processAnimation: function(){},
            doneAnimation: function(){}
        };
        this.element = element;
        this.currentFrame = 0;
        this.lastFrame = 0;
        this.frameNum = 0;
        this.startTime = window.requestAnimationFrame.now();
        //requestId = null;
        this.timeIndex = 1;

        options && typeof options == 'object' && this.setOptions(options);

        // this.options.fps=6;

    };
    var requestId = null;
    var canceled = 0;

    reqAnimate.prototype.start = function() {
        canceled = 0;
        this.loop(this.startTime);
    };

    reqAnimate.prototype.cancel = function() {
        if (requestId) {
            window.cancelAnimationFrame(requestId);
            requestId = null;
            canceled = 1;
        }
    };

    reqAnimate.prototype.setOptions = function(options){
        // shallow copy
        var o = this.options,
            key;

        for (key in options) options.hasOwnProperty(key) && (o[key] = options[key]);

        return this;
    };

    reqAnimate.prototype.loop = function(time) {
        var o = this.options;
        if (!canceled) {
            this.updateFrame(time);
            requestId = window.requestAnimationFrame(this.loop.bind(this));

            if ((this.frameNum + 1 == o.totalFrames) && o.time !== Infinity && this.timeIndex >= o.time) {
                this.cancel();
                o.doneAnimation && o.doneAnimation.call(this, this.element);
            }
        }
    };

    reqAnimate.prototype.updateFrame = function(time) {
        var o = this.options
            , delta = (time - this.startTime) / 1000;
        this.currentFrame += (delta * o.fps);

        this.frameNum = Math.floor(this.currentFrame);

        if (this.frameNum >= o.totalFrames) {
            this.currentFrame = this.frameNum = 0;
            this.timeIndex++;
            this.lastFrame = -1;
        }

        this.frameNum >= 0
        && this.frameNum > this.lastFrame
        && o.processAnimation
        && o.processAnimation.call(this, this.element, this.frameNum);

        this.startTime = window.requestAnimationFrame.now();
        this.lastFrame = this.frameNum;
    };

    if (typeof define === 'function' && define.amd){
        // we have an AMD loader.
        define(function(){
            return reqAnimate;
        });
    }
    else {
        this.reqAnimate = reqAnimate;
    }

}).call(this);