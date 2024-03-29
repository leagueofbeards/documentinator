((function () {
    var a, b, c, d, e, f, g, h, i, j, k, l, m, n, o, p, q, r, s, t, u, v = Array.prototype.slice,
        w = Object.prototype.hasOwnProperty,
        x = function (a, b) {
            return function () {
                return a.apply(b, arguments)
            }
        }, y = function (a, b) {
            function d() {
                this.constructor = a
            }
            for (var c in b) w.call(b, c) && (a[c] = b[c]);
            return d.prototype = b.prototype, a.prototype = new d, a.__super__ = b.prototype, a
        }, z = Array.prototype.indexOf || function (a) {
            for (var b = 0, c = this.length; b < c; b++) if (b in this && this[b] === a) return b;
            return -1
        };
    k = null, typeof Gettext != "undefined" && Gettext !== null ? (o = new Gettext({
        domain: "annotator"
    }), k = function (a) {
        return o.gettext(a)
    }) : k = function (a) {
        return a
    }, u = function (a) {
        return k(a)
    }, (typeof jQuery != "undefined" && jQuery !== null ? (t = jQuery.fn) != null ? t.jquery : void 0 : void 0) || console.error(u("Annotator requires jQuery: have you included lib/vendor/jquery.js?")), JSON && JSON.parse && JSON.stringify || console.error(u("Annotator requires a JSON implementation: have you included lib/vendor/json2.js?")), a = jQuery.sub(), a.flatten = function (b) {
        var c;
        return c = function (b) {
            var d, e, f, g;
            e = [];
            for (f = 0, g = b.length; f < g; f++) d = b[f], e = e.concat(d && a.isArray(d) ? c(d) : d);
            return e
        }, c(b)
    }, a.plugin = function (b, c) {
        return jQuery.fn[b] = function (d) {
            var e;
            return e = Array.prototype.slice.call(arguments, 1), this.each(function () {
                var f;
                return f = a.data(this, b), f ? d && f[d].apply(f, e) : (f = new c(this, d), a.data(this, b, f))
            })
        }
    }, a.fn.textNodes = function () {
        var b;
        return b = function (a) {
            var c;
            if (a && a.nodeType !== 3) {
                c = [];
                if (a.nodeType !== 8) {
                    a = a.lastChild;
                    while (a) c.push(b(a)), a = a.previousSibling
                }
                return c.reverse()
            }
            return a
        }, this.map(function () {
            return a.flatten(b(this))
        })
    }, a.fn.xpath = function (b) {
        var c;
        return c = this.map(function () {
            var c, d, e;
            e = "", c = this;
            while (c && c.nodeType === 1 && c !== b) d = a(c.parentNode).children(c.tagName).index(c) + 1, d = d > 1 ? "[" + d + "]" : "", e = "/" + c.tagName.toLowerCase() + d + e, c = c.parentNode;
            return e
        }), c.get()
    }, a.escape = function (a) {
        return a.replace(/&(?!\w+;)/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;")
    }, a.fn.escape = function (b) {
        return arguments.length ? this.html(a.escape(b)) : this.html()
    }, j = ["log", "debug", "info", "warn", "exception", "assert", "dir", "dirxml", "trace", "group", "groupEnd", "groupCollapsed", "time", "timeEnd", "profile", "profileEnd", "count", "clear", "table", "error", "notifyFirebug", "firebug", "userObjects"];
    if (typeof console != "undefined" && console !== null) {
        console.group == null && (console.group = function (a) {
            return console.log("GROUP: ", a)
        }), console.groupCollapsed == null && (console.groupCollapsed = console.group);
        for (p = 0, r = j.length; p < r; p++) i = j[p], console[i] == null && (console[i] = function () {
            return console.log(u("Not implemented:") + (" console." + name))
        })
    } else {
        this.console = {};
        for (q = 0, s = j.length; q < s; q++) i = j[q], this.console[i] = function () {};
        this.console.error = function () {
            var a;
            return a = 1 <= arguments.length ? v.call(arguments, 0) : [], alert("ERROR: " + a.join(", "))
        }, this.console.warn = function () {
            var a;
            return a = 1 <= arguments.length ? v.call(arguments, 0) : [], alert("WARNING: " + a.join(", "))
        }
    }
    c = function () {
        function b(b, c) {
            this.options = a.extend(!0, {}, this.options, c), this.element = a(b), this.on = this.subscribe, this.addEvents()
        }
        return b.prototype.events = {}, b.prototype.options = {}, b.prototype.element = null, b.prototype.addEvents = function () {
            var a, b, c, d, e, f, g, h;
            f = this.events, h = [];
            for (c in f) b = f[c], g = c.split(" "), d = 2 <= g.length ? v.call(g, 0, e = g.length - 1) : (e = 0, []), a = g[e++], h.push(this.addEvent(d.join(" "), a, b));
            return h
        }, b.prototype.addEvent = function (b, c, d) {
            var e, f, g = this;
            return e = function () {
                return g[d].apply(g, arguments)
            }, f = typeof b == "string" && b.replace(/\s+/g, "") === "", f && (b = this.element), typeof b == "string" ? this.element.delegate(b, c, e) : this.isCustomEvent(c) ? this.subscribe(c, e) : a(b).bind(c, e), this
        }, b.prototype.isCustomEvent = function (c) {
            return c = c.split(".")[0], a.inArray(c, b.natives) === -1
        }, b.prototype.publish = function () {
            return this.element.triggerHandler.apply(this.element, arguments), this
        }, b.prototype.subscribe = function (b, c) {
            var d;
            return d = function () {
                return c.apply(this, [].slice.call(arguments, 1))
            }, d.guid = c.guid = a.guid += 1, this.element.bind(b, d), this
        }, b.prototype.unsubscribe = function () {
            return this.element.unbind.apply(this.element, arguments), this
        }, b
    }(), c.natives = function () {
        var a, b, c;
        return b = function () {
            var b, d;
            b = jQuery.event.special, d = [];
            for (a in b) {
                if (!w.call(b, a)) continue;
                c = b[a], d.push(a)
            }
            return d
        }(), "blur focus focusin focusout load resize scroll unload click dblclick\nmousedown mouseup mousemove mouseover mouseout mouseenter mouseleave\nchange select submit keydown keypress keyup error".split(/[^a-z]+/).concat(b)
    }(), e = {}, e.sniff = function (a) {
        return a.commonAncestorContainer != null ? new e.BrowserRange(a) : typeof a.start == "string" ? new e.SerializedRange(a) : a.start && typeof a.start == "object" ? new e.NormalizedRange(a) : (console.error(u("Could not sniff range type")), !1)
    }, e.BrowserRange = function () {
        function a(a) {
            this.commonAncestorContainer = a.commonAncestorContainer, this.startContainer = a.startContainer, this.startOffset = a.startOffset, this.endContainer = a.endContainer, this.endOffset = a.endOffset
        }
        return a.prototype.normalize = function (a) {
            var b, c, d, f, g, h, i, j, k;
            if (this.tainted) return console.error(u("You may only call normalize() once on a BrowserRange!")), !1;
            this.tainted = !0, h = {}, d = {}, k = ["start", "end"];
            for (i = 0, j = k.length; i < j; i++) {
                g = k[i], c = this[g + "Container"], f = this[g + "Offset"];
                if (c.nodeType === 1) {
                    b = c.childNodes[f], c = b || c.childNodes[f - 1], c.nodeType === 1 && !c.firstChild && (b = null, c = c.previousSibling);
                    while (c.nodeType !== 3) c = c.firstChild;
                    f = b ? 0 : c.nodeValue.length
                }
                h[g] = c, h[g + "Offset"] = f
            }
            d.start = h.startOffset > 0 ? h.start.splitText(h.startOffset) : h.start, h.start === h.end ? (h.endOffset - h.startOffset < d.start.nodeValue.length && d.start.splitText(h.endOffset - h.startOffset), d.end = d.start) : (h.endOffset < h.end.nodeValue.length && h.end.splitText(h.endOffset), d.end = h.end), d.commonAncestor = this.commonAncestorContainer;
            while (d.commonAncestor.nodeType !== 1) d.commonAncestor = d.commonAncestor.parentNode;
            return new e.NormalizedRange(d)
        }, a.prototype.serialize = function (a, b) {
            return this.normalize(a).serialize(a, b)
        }, a
    }(), e.NormalizedRange = function () {
        function b(a) {
            this.commonAncestor = a.commonAncestor, this.start = a.start, this.end = a.end
        }
        return b.prototype.normalize = function (a) {
            return this
        }, b.prototype.limit = function (b) {
            var c, d, e, f, g, h;
            c = a.grep(this.textNodes(), function (c) {
                return c.parentNode === b || a.contains(b, c.parentNode)
            });
            if (!c.length) return null;
            this.start = c[0], this.end = c[c.length - 1], e = a(this.start).parents(), h = a(this.end).parents();
            for (f = 0, g = h.length; f < g; f++) {
                d = h[f];
                if (e.index(d) !== -1) {
                    this.commonAncestor = d;
                    break
                }
            }
            return this
        }, b.prototype.serialize = function (b, c) {
            var d, f, g;
            return f = function (d, e) {
                var f, g, h, i, j, k, l, m;
                c ? i = a(d).parents(":not(" + c + ")").eq(0) : i = a(d).parent(), k = i.xpath(b)[0], j = i.textNodes(), g = j.slice(0, j.index(d)), h = 0;
                for (l = 0, m = g.length; l < m; l++) f = g[l], h += f.nodeValue.length;
                return e ? [k, h + d.nodeValue.length] : [k, h]
            }, g = f(this.start), d = f(this.end, !0), new e.SerializedRange({
                start: g[0],
                end: d[0],
                startOffset: g[1],
                endOffset: d[1]
            })
        }, b.prototype.text = function () {
            var a;
            return function () {
                var b, c, d, e;
                d = this.textNodes(), e = [];
                for (b = 0, c = d.length; b < c; b++) a = d[b], e.push(a.nodeValue);
                return e
            }.call(this).join("")
        }, b.prototype.textNodes = function () {
            var b, c, d, e;
            return d = a(this.commonAncestor).textNodes(), e = [d.index(this.start), d.index(this.end)], c = e[0], b = e[1], a.makeArray(d.slice(c, b + 1 || 9e9))
        }, b.prototype.toRange = function () {
            var a;
            return a = document.createRange(), a.setStartBefore(this.start), a.setEndAfter(this.end), a
        }, b
    }(), e.SerializedRange = function () {
        function b(a) {
            this.start = a.start, this.startOffset = a.startOffset, this.end = a.end, this.endOffset = a.endOffset
        }
        return b.prototype._nodeFromXPath = function (b) {
            var c, d, e, f, g;
            return d = function (a, b) {
                return b == null && (b = null), document.evaluate(a, document, b, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue
            }, a.isXMLDoc(document.documentElement) ? (c = document.createNSResolver(document.ownerDocument === null ? document.documentElement : document.ownerDocument.documentElement), f = d(b, c), f || (b = function () {
                var a, c, d, e;
                d = b.split("/"), e = [];
                for (a = 0, c = d.length; a < c; a++) g = d[a], g && g.indexOf(":") === -1 ? e.push(g.replace(/^([a-z]+)/, "xhtml:$1")) : e.push(g);
                return e
            }().join("/"), e = document.lookupNamespaceURI(null), c = function (a) {
                return a === "xhtml" ? e : document.documentElement.getAttribute("xmlns:" + a)
            }, f = d(b, c)), f) : d(b)
        }, b.prototype.normalize = function (b) {
            var c, d, f, g, h, i, j, k, l, m, n, o, p, q, r, s, t;
            j = a(b).xpath()[0], l = this.start.split("/"), f = this.end.split("/"), d = [], k = {};
            for (g = 0, r = l.length; 0 <= r ? g < r : g > r; 0 <= r ? g++ : g--) {
                if (l[g] !== f[g]) break;
                d.push(l[g])
            }
            c = j + d.join("/"), k.commonAncestorContainer = this._nodeFromXPath(c);
            if (!k.commonAncestorContainer) return console.error(u("Error deserializing range: can't find XPath '") + c + u("'. Is this the right document?")), null;
            s = ["start", "end"];
            for (n = 0, p = s.length; n < p; n++) {
                i = s[n], h = 0, t = a(this._nodeFromXPath(j + this[i])).textNodes();
                for (o = 0, q = t.length; o < q; o++) {
                    m = t[o];
                    if (h + m.nodeValue.length >= this[i + "Offset"]) {
                        k[i + "Container"] = m, k[i + "Offset"] = this[i + "Offset"] - h;
                        break
                    }
                    h += m.nodeValue.length
                }
            }
            return (new e.BrowserRange(k)).normalize(b)
        }, b.prototype.serialize = function (a, b) {
            return this.normalize(a).serialize(a, b)
        }, b.prototype.toObject = function () {
            return {
                start: this.start,
                startOffset: this.startOffset,
                end: this.end,
                endOffset: this.endOffset
            }
        }, b
    }(), m = {
        uuid: function () {
            var a;
            return a = 0,
            function () {
                return a++
            }
        }(),
        getGlobal: function () {
            return function () {
                return this
            }()
        },
        mousePosition: function (b, c) {
            var d;
            return d = a(c).offset(), {
                top: b.pageY - d.top,
                left: b.pageX - d.left
            }
        },
        preventEventDefault: function (a) {
            return a != null ? typeof a.preventDefault == "function" ? a.preventDefault() : void 0 : void 0
        }
    }, n = this.Annotator, b = function (b) {
        function c(b, d) {
            this.onDeleteAnnotation = x(this.onDeleteAnnotation, this), this.onEditAnnotation = x(this.onEditAnnotation, this), this.onAdderClick = x(this.onAdderClick, this), this.onAdderMousedown = x(this.onAdderMousedown, this), this.onHighlightMouseover = x(this.onHighlightMouseover, this), this.checkForEndSelection = x(this.checkForEndSelection, this), this.checkForStartSelection = x(this.checkForStartSelection, this), this.clearViewerHideTimer = x(this.clearViewerHideTimer, this), this.startViewerHideTimer = x(this.startViewerHideTimer, this), this.showViewer = x(this.showViewer, this), this.onEditorSubmit = x(this.onEditorSubmit, this), this.onEditorHide = x(this.onEditorHide, this), this.showEditor = x(this.showEditor, this);
            var e, f, g;
            c.__super__.constructor.apply(this, arguments), this.plugins = {};
            if (!c.supported()) return this;
            this.options.readOnly || this._setupDocumentEvents(), this._setupWrapper()._setupViewer()._setupEditor(), g = this.html;
            for (e in g) f = g[e], e !== "wrapper" && (this[e] = a(f).appendTo(this.wrapper).hide())
        }
        return y(c, b), c.prototype.events = {
            ".annotator-adder button click": "onAdderClick",
            ".annotator-adder button mousedown": "onAdderMousedown",
            ".annotator-hl mouseover": "onHighlightMouseover",
            ".annotator-hl mouseout": "startViewerHideTimer"
        }, c.prototype.html = {
            hl: '<span class="annotator-hl"></span>',
            adder: '<div class="annotator-adder"><button>' + u("Annotate") + "</button></div>",
            wrapper: '<div class="annotator-wrapper"></div>'
        }, c.prototype.options = {
            readOnly: !1
        }, c.prototype.plugins = {}, c.prototype.editor = null, c.prototype.viewer = null, c.prototype.selectedRanges = null, c.prototype.mouseIsDown = !1, c.prototype.ignoreMouseup = !1, c.prototype.viewerHideTimer = null, c.prototype._setupWrapper = function () {
            return this.wrapper = a(this.html.wrapper), this.element.find("script").remove(), this.element.wrapInner(this.wrapper), this.wrapper = this.element.find(".annotator-wrapper"), this
        }, c.prototype._setupViewer = function () {
            var b = this;
            return this.viewer = new c.Viewer({
                readOnly: this.options.readOnly
            }), this.viewer.hide().on("edit", this.onEditAnnotation).on("delete", this.onDeleteAnnotation).addField({
                load: function (c, d) {
                    return d.text ? a(c).escape(d.text) : a(c).html("<i>" + u("No Comment") + "</i>"), b.publish("annotationViewerTextField", [c, d])
                }
            }).element.appendTo(this.wrapper).bind({
                mouseover: this.clearViewerHideTimer,
                mouseout: this.startViewerHideTimer
            }), this
        }, c.prototype._setupEditor = function () {
            return this.editor = new c.Editor, this.editor.hide().on("hide", this.onEditorHide).on("save", this.onEditorSubmit).addField({
                type: "textarea",
                label: u("Comments") + "…",
                load: function (b, c) {
                    return a(b).find("textarea").val(c.text || "")
                },
                submit: function (b, c) {
                    return c.text = a(b).find("textarea").val()
                }
            }), this.editor.element.appendTo(this.wrapper), this
        }, c.prototype._setupDocumentEvents = function () {
            return a(document).bind({
                mouseup: this.checkForEndSelection,
                mousedown: this.checkForStartSelection
            }), this
        }, c.prototype.getSelectedRanges = function () {
            var b, c, d, f, g, h, i, j, k;
            i = m.getGlobal().getSelection(), g = [], h = [], i.isCollapsed || (g = function () {
                var a, g;
                g = [];
                for (c = 0, a = i.rangeCount; 0 <= a ? c < a : c > a; 0 <= a ? c++ : c--) f = i.getRangeAt(c), b = new e.BrowserRange(f), d = b.normalize().limit(this.wrapper[0]), d === null && h.push(f), g.push(d);
                return g
            }.call(this), i.removeAllRanges());
            for (j = 0, k = h.length; j < k; j++) f = h[j], i.addRange(f);
            return a.grep(g, function (a) {
                return a && i.addRange(a.toRange()), a
            })
        }, c.prototype.createAnnotation = function () {
            var a;
            return a = {}, this.publish("beforeAnnotationCreated", [a]), a
        }, c.prototype.setupAnnotation = function (b, c) {
            var d, f, g, h, i, j;
            c == null && (c = !0), b.ranges || (b.ranges = this.selectedRanges), f = function () {
                var a, c, d, f;
                d = b.ranges, f = [];
                for (a = 0, c = d.length; a < c; a++) g = d[a], h = e.sniff(g), f.push(h.normalize(this.wrapper[0]));
                return f
            }.call(this), f = a.grep(f, function (a) {
                return a !== null
            }), b.quote = [], b.ranges = [], b.highlights = [];
            for (i = 0, j = f.length; i < j; i++) d = f[i], b.quote.push(a.trim(d.text())), b.ranges.push(d.serialize(this.wrapper[0], ".annotator-hl")), a.merge(b.highlights, this.highlightRange(d));
            return b.quote = b.quote.join(" / "), a(b.highlights).data("annotation", b), c && this.publish("annotationCreated", [b]), b
        }, c.prototype.updateAnnotation = function (a) {
            return this.publish("beforeAnnotationUpdated", [a]), this.publish("annotationUpdated", [a]), a
        }, c.prototype.deleteAnnotation = function (b) {
            var c, d, e, f;
            f = b.highlights;
            for (d = 0, e = f.length; d < e; d++) c = f[d], a(c).replaceWith(c.childNodes);
            return this.publish("annotationDeleted", [b]), b
        }, c.prototype.loadAnnotations = function (a) {
            var b, c, d = this;
            return a == null && (a = []), c = function (a) {
                var e, f, g, h;
                a == null && (a = []), f = a.splice(0, 10);
                for (g = 0, h = f.length; g < h; g++) e = f[g], d.setupAnnotation(e, !1);
                return a.length > 0 ? setTimeout(function () {
                    return c(a)
                }, 100) : d.publish("annotationsLoaded", [b])
            }, b = a.slice(), a.length && c(a), this
        }, c.prototype.dumpAnnotations = function () {
            return this.plugins.Store ? this.plugins.Store.dumpAnnotations() : console.warn(u("Can't dump annotations without Store plugin."))
        }, c.prototype.highlightRange = function (b) {
            var c, d, e, f, g, h;
            d = /^\s*$/, g = b.textNodes(), h = [];
            for (e = 0, f = g.length; e < f; e++) c = g[e], d.test(c.nodeValue) || h.push(a(c).wrapAll(this.hl).parent().show()[0]);
            return h
        }, c.prototype.addPlugin = function (a, b) {
            var d, e;
            return this.plugins[a] ? console.error(u("You cannot have more than one instance of any plugin.")) : (d = c.Plugin[a], typeof d == "function" ? (this.plugins[a] = new d(this.element[0], b), this.plugins[a].annotator = this, typeof (e = this.plugins[a]).pluginInit == "function" && e.pluginInit()) : console.error(u("Could not load ") + a + u(" plugin. Have you included the appropriate <script> tag?"))), this
        }, c.prototype.showEditor = function (a, b) {
            return this.editor.element.css(b), this.editor.load(a), this
        }, c.prototype.onEditorHide = function () {
            return this.publish("annotationEditorHidden", [this.editor]), this.ignoreMouseup = !1
        }, c.prototype.onEditorSubmit = function (a) {
            return this.publish("annotationEditorSubmit", [this.editor, a]), a.ranges === void 0 ? this.setupAnnotation(a) : this.updateAnnotation(a)
        }, c.prototype.showViewer = function (a, b) {
            return this.viewer.element.css(b), this.viewer.load(a), this.publish("annotationViewerShown", [this.viewer, a])
        }, c.prototype.startViewerHideTimer = function () {
            if (!this.viewerHideTimer) return this.viewerHideTimer = setTimeout(this.viewer.hide, 250)
        }, c.prototype.clearViewerHideTimer = function () {
            return clearTimeout(this.viewerHideTimer), this.viewerHideTimer = !1
        }, c.prototype.checkForStartSelection = function (a) {
            if (!a || !this.isAnnotator(a.target)) return this.startViewerHideTimer(), this.mouseIsDown = !0
        }, c.prototype.checkForEndSelection = function (a) {
            var b, c, d, e, f;
            this.mouseIsDown = !1;
            if (this.ignoreMouseup) return;
            this.selectedRanges = this.getSelectedRanges(), f = this.selectedRanges;
            for (d = 0, e = f.length; d < e; d++) {
                c = f[d], b = c.commonAncestor;
                if (this.isAnnotator(b)) return
            }
            return a && this.selectedRanges.length ? this.adder.css(m.mousePosition(a, this.wrapper[0])).show() : this.adder.hide()
        }, c.prototype.isAnnotator = function (b) {
            return !!a(b).parents().andSelf().filter("[class^=annotator-]").not(this.wrapper).length
        }, c.prototype.onHighlightMouseover = function (b) {
            var c;
            return this.clearViewerHideTimer(), this.mouseIsDown || this.viewer.isShown() ? !1 : (c = a(b.target).parents(".annotator-hl").andSelf().map(function () {
                return a(this).data("annotation")
            }), this.showViewer(a.makeArray(c), m.mousePosition(b, this.wrapper[0])))
        }, c.prototype.onAdderMousedown = function (a) {
            return a != null && a.preventDefault(), this.ignoreMouseup = !0
        }, c.prototype.onAdderClick = function (a) {
            var b;
            return a != null && a.preventDefault(), b = this.adder.position(), this.adder.hide(), this.showEditor(this.createAnnotation(), b)
        }, c.prototype.onEditAnnotation = function (a) {
            var b;
            return b = this.viewer.element.position(), this.viewer.hide(), this.showEditor(a, b)
        }, c.prototype.onDeleteAnnotation = function (a) {
            return this.viewer.hide(), this.deleteAnnotation(a)
        }, c
    }(c), b.Plugin = function (a) {
        function b(a, c) {
            b.__super__.constructor.apply(this, arguments)
        }
        return y(b, a), b.prototype.pluginInit = function () {}, b
    }(c), b.$ = a, b.Delegator = c, b.Range = e, b._t = u, b.supported = function () {
        return function () {
            return !!this.getSelection
        }()
    }, b.noConflict = function () {
        return m.getGlobal().Annotator = n, this
    }, a.plugin("annotator", b), this.Annotator = b, b.Widget = function (c) {
        function d(c, e) {
            d.__super__.constructor.apply(this, arguments), this.classes = a.extend({}, b.Widget.prototype.classes, this.classes)
        }
        return y(d, c), d.prototype.classes = {
            hide: "annotator-hide",
            invert: {
                x: "annotator-invert-x",
                y: "annotator-invert-y"
            }
        }, d.prototype.checkOrientation = function () {
            var b, c, d, e, f;
            return this.resetOrientation(), f = a(m.getGlobal()), e = this.element.children(":first"), c = e.offset(), d = {
                top: f.scrollTop(),
                right: f.width() + f.scrollLeft()
            }, b = {
                top: c.top,
                right: c.left + e.width()
            }, b.top - d.top < 0 && this.invertY(), b.right - d.right > 0 && this.invertX(), this
        }, d.prototype.resetOrientation = function () {
            return this.element.removeClass(this.classes.invert.x).removeClass(this.classes.invert.y), this
        }, d.prototype.invertX = function () {
            return this.element.addClass(this.classes.invert.x), this
        }, d.prototype.invertY = function () {
            return this.element.addClass(this.classes.invert.y), this
        }, d.prototype.isInvertedY = function () {
            return this.element.hasClass(this.classes.invert.y)
        }, d.prototype.isInvertedX = function () {
            return this.element.hasClass(this.classes.invert.x)
        }, d
    }(c), b.Editor = function (b) {
        function c(b) {
            this.onCancelButtonMouseover = x(this.onCancelButtonMouseover, this), this.processKeypress = x(this.processKeypress, this), this.submit = x(this.submit, this), this.load = x(this.load, this), this.hide = x(this.hide, this), this.show = x(this.show, this), c.__super__.constructor.call(this, a(this.html)[0], b), this.fields = [], this.annotation = {}
        }
        return y(c, b), c.prototype.events = {
            "form submit": "submit",
            ".annotator-save click": "submit",
            ".annotator-cancel click": "hide",
            ".annotator-cancel mouseover": "onCancelButtonMouseover",
            "textarea keydown": "processKeypress"
        }, c.prototype.classes = {
            hide: "annotator-hide",
            focus: "annotator-focus"
        }, c.prototype.html = '<div class="annotator-outer annotator-editor">\n  <form class="annotator-widget">\n    <ul class="annotator-listing"></ul>\n    <div class="annotator-controls">\n      <a href="#cancel" class="annotator-cancel">' + u("Cancel") + '</a>\n<a href="#save" class="annotator-save annotator-focus">' + u("Save") + "</a>\n    </div>\n  </form>\n</div>", c.prototype.options = {}, c.prototype.show = function (a) {
            return m.preventEventDefault(a), this.element.removeClass(this.classes.hide), this.element.find(".annotator-save").addClass(this.classes.focus), this.checkOrientation(), this.element.find(":input:first").focus(), this.setupDraggables(), this.publish("show")
        }, c.prototype.hide = function (a) {
            return m.preventEventDefault(a), this.element.addClass(this.classes.hide), this.publish("hide")
        }, c.prototype.load = function (a) {
            var b, c, d, e;
            this.annotation = a, this.publish("load", [this.annotation]), e = this.fields;
            for (c = 0, d = e.length; c < d; c++) b = e[c], b.load(b.element, this.annotation);
            return this.show()
        }, c.prototype.submit = function (a) {
            var b, c, d, e;
            m.preventEventDefault(a), e = this.fields;
            for (c = 0, d = e.length; c < d; c++) b = e[c], b.submit(b.element, this.annotation);
            return this.publish("save", [this.annotation]), this.hide()
        }, c.prototype.addField = function (b) {
            var c, d, e;
            d = a.extend({
                id: "annotator-field-" + m.uuid(),
                type: "input",
                label: "",
                load: function () {},
                submit: function () {}
            }, b), e = null, c = a('<li class="annotator-item" />'), d.element = c[0];
            switch (d.type) {
                case "textarea":
                    e = a("<textarea />");
                    break;
                case "input":
                case "checkbox":
                    e = a("<input />")
            }
            return c.append(e), e.attr({
                id: d.id,
                placeholder: d.label
            }), d.type === "checkbox" && (e[0].type = "checkbox", c.addClass("annotator-checkbox"), c.append(a("<label />", {
                "for": d.id,
                html: d.label
            }))), this.element.find("ul:first").append(c), this.fields.push(d), d.element
        }, c.prototype.checkOrientation = function () {
            var a, b;
            return c.__super__.checkOrientation.apply(this, arguments), b = this.element.find("ul"), a = this.element.find(".annotator-controls"), this.element.hasClass(this.classes.invert.y) ? a.insertBefore(b) : a.is(":first-child") && a.insertAfter(b), this
        }, c.prototype.processKeypress = function (a) {
            if (a.keyCode === 27) return this.hide();
            if (a.keyCode === 13 && !a.shiftKey) return this.submit()
        }, c.prototype.onCancelButtonMouseover = function () {
            return this.element.find("." + this.classes.focus).removeClass(this.classes.focus)
        }, c.prototype.setupDraggables = function () {
            var b, c, d, e, f, g, h, i, j, k, l, m = this;
            return this.element.find(".annotator-resize").remove(), this.element.hasClass(this.classes.invert.y) ? d = this.element.find(".annotator-item:last") : d = this.element.find(".annotator-item:first"), d && a('<span class="annotator-resize"></span>').appendTo(d), f = null, b = this.classes, e = this.element, k = null, j = e.find(".annotator-resize"), c = e.find(".annotator-controls"), l = !1, g = function (b) {
                if (b.target === this) return f = {
                    element: this,
                    top: b.pageY,
                    left: b.pageX
                }, k = e.find("textarea:first"), a(window).bind({
                    "mouseup.annotator-editor-resize": i,
                    "mousemove.annotator-editor-resize": h
                }), b.preventDefault()
            }, i = function () {
                return f = null, a(window).unbind(".annotator-editor-resize")
            }, h = function (a) {
                var d, g, h, i, m;
                if (f && l === !1) return d = {
                    top: a.pageY - f.top,
                    left: a.pageX - f.left
                }, f.element === j[0] ? (i = k.outerHeight(), m = k.outerWidth(), g = e.hasClass(b.invert.x) ? -1 : 1, h = e.hasClass(b.invert.y) ? 1 : -1, k.height(i + d.top * h), k.width(m + d.left * g), k.outerHeight() !== i && (f.top = a.pageY), k.outerWidth() !== m && (f.left = a.pageX)) : f.element === c[0] && (e.css({
                    top: parseInt(e.css("top"), 10) + d.top,
                    left: parseInt(e.css("left"), 10) + d.left
                }), f.top = a.pageY, f.left = a.pageX), l = !0, setTimeout(function () {
                    return l = !1
                }, 1e3 / 60)
            }, j.bind("mousedown", g), c.bind("mousedown", g)
        }, c
    }(b.Widget), b.Viewer = function (b) {
        function c(b) {
            this.onDeleteClick = x(this.onDeleteClick, this), this.onEditClick = x(this.onEditClick, this), this.load = x(this.load, this), this.hide = x(this.hide, this), this.show = x(this.show, this), c.__super__.constructor.call(this, a(this.html.element)[0], b), this.item = a(this.html.item)[0], this.fields = [], this.annotations = []
        }
        return y(c, b), c.prototype.events = {
            ".annotator-edit click": "onEditClick",
            ".annotator-delete click": "onDeleteClick"
        }, c.prototype.classes = {
            hide: "annotator-hide",
            showControls: "annotator-visible"
        }, c.prototype.html = {
            element: '<div class="annotator-outer annotator-viewer">\n  <ul class="annotator-widget annotator-listing"></ul>\n</div>',
            item: '<li class="annotator-annotation annotator-item">\n  <span class="annotator-controls">\n    <a href="#" title="View as webpage" class="annotator-link">View as webpage</a>\n    <button title="Edit" class="annotator-edit">Edit</button>\n    <button title="Delete" class="annotator-delete">Delete</button>\n  </span>\n</li>'
        }, c.prototype.options = {
            readOnly: !1
        }, c.prototype.show = function (a) {
            var b, c = this;
            return m.preventEventDefault(a), b = this.element.find(".annotator-controls").addClass(this.classes.showControls), setTimeout(function () {
                return b.removeClass(c.classes.showControls)
            }, 500), this.element.removeClass(this.classes.hide), this.checkOrientation().publish("show")
        }, c.prototype.isShown = function () {
            return !this.element.hasClass(this.classes.hide)
        }, c.prototype.hide = function (a) {
            return m.preventEventDefault(a), this.element.addClass(this.classes.hide), this.publish("hide")
        }, c.prototype.load = function (b) {
            var c, e, f, g, h, i, j, k, l, m, n, o, p, q, r, s, t;
            this.annotations = b || [], n = this.element.find("ul:first").empty(), s = this.annotations;
            for (o = 0, q = s.length; o < q; o++) {
                c = s[o], k = a(this.item).clone().appendTo(n).data("annotation", c), f = k.find(".annotator-controls"), l = f.find(".annotator-link"), h = f.find(".annotator-edit"), g = f.find(".annotator-delete"), m = (new d(c.links || [])).get("alternate", {
                    type: "text/html"
                }), m.length === 0 || m[0].href == null ? l.remove() : l.attr("href", m[0].href), this.options.readOnly ? (h.remove(), g.remove()) : e = {
                    showEdit: function () {
                        return h.removeAttr("disabled")
                    },
                    hideEdit: function () {
                        return h.attr("disabled", "disabled")
                    },
                    showDelete: function () {
                        return g.removeAttr("disabled")
                    },
                    hideDelete: function () {
                        return g.attr("disabled", "disabled")
                    }
                }, t = this.fields;
                for (p = 0, r = t.length; p < r; p++) j = t[p], i = a(j.element).clone().appendTo(k)[0], j.load(i, c, e)
            }
            return this.publish("load", [this.annotations]), this.show()
        }, c.prototype.addField = function (b) {
            var c;
            return c = a.extend({
                load: function () {}
            }, b), c.element = a("<div />")[0], this.fields.push(c), c.element, this
        }, c.prototype.onEditClick = function (a) {
            return this.onButtonClick(a, "edit")
        }, c.prototype.onDeleteClick = function (a) {
            return this.onButtonClick(a, "delete")
        }, c.prototype.onButtonClick = function (b, c) {
            var d;
            return d = a(b.target).parents(".annotator-annotation"), this.publish(c, [d.data("annotation")])
        }, c
    }(b.Widget), d = function () {
        function b(a) {
            this.data = a
        }
        return b.prototype.get = function (b, c) {
            var d, e, f, g, h, i, j, k, l;
            c == null && (c = {}), c = a.extend({}, c, {
                rel: b
            }), f = function () {
                var a;
                a = [];
                for (e in c) {
                    if (!w.call(c, e)) continue;
                    h = c[e], a.push(e)
                }
                return a
            }(), k = this.data, l = [];
            for (i = 0, j = k.length; i < j; i++) {
                d = k[i], g = f.reduce(function (a, b) {
                    return a && d[b] === c[b]
                }, !0);
                if (!g) continue;
                l.push(d)
            }
            return l
        }, b
    }(), b = b || {}, b.Notification = function (c) {
        function d(b) {
            this.hide = x(this.hide, this), this.show = x(this.show, this), d.__super__.constructor.call(this, a(this.options.html).appendTo(document.body)[0], b)
        }
        return y(d, c), d.prototype.events = {
            click: "hide"
        }, d.prototype.options = {
            html: "<div class='annotator-notice'></div>",
            classes: {
                show: "annotator-notice-show",
                info: "annotator-notice-info",
                success: "annotator-notice-success",
                error: "annotator-notice-error"
            }
        }, d.prototype.show = function (c, d) {
            return d == null && (d = b.Notification.INFO), a(this.element).addClass(this.options.classes.show).addClass(this.options.classes[d]).escape(c || ""), setTimeout(this.hide, 5e3), this
        }, d.prototype.hide = function () {
            return a(this.element).removeClass(this.options.classes.show), this
        }, d
    }(c), b.Notification.INFO = "show", b.Notification.SUCCESS = "success", b.Notification.ERROR = "error", a(function () {
        var a;
        return a = new b.Notification, b.showNotification = a.show, b.hideNotification = a.hide
    }), b.Plugin.Tags = function (c) {
        function d() {
            this.setAnnotationTags = x(this.setAnnotationTags, this), this.updateField = x(this.updateField, this), d.__super__.constructor.apply(this, arguments)
        }
        return y(d, c), d.prototype.options = {
            parseTags: function (b) {
                var c;
                return b = a.trim(b), c = [], b && (c = b.split(/\s+/)), c
            },
            stringifyTags: function (a) {
                return a.join(" ")
            }
        }, d.prototype.field = null, d.prototype.input = null, d.prototype.pluginInit = function () {
            if (!b.supported()) return;
            return this.field = this.annotator.editor.addField({
                label: b._t("Add some tags here") + "…",
                load: this.updateField,
                submit: this.setAnnotationTags
            }), this.annotator.viewer.addField({
                load: this.updateViewer
            }), this.annotator.plugins.Filter && this.annotator.plugins.Filter.addFilter({
                label: b._t("Tag"),
                property: "tags",
                isFiltered: b.Plugin.Tags.filterCallback
            }), this.input = a(this.field).find(":input")
        }, d.prototype.parseTags = function (a) {
            return this.options.parseTags(a)
        }, d.prototype.stringifyTags = function (a) {
            return this.options.stringifyTags(a)
        }, d.prototype.updateField = function (a, b) {
            var c;
            return c = "", b.tags && (c = this.stringifyTags(b.tags)), this.input.val(c)
        }, d.prototype.setAnnotationTags = function (a, b) {
            return b.tags = this.parseTags(this.input.val())
        }, d.prototype.updateViewer = function (c, d) {
            return c = a(c), d.tags && a.isArray(d.tags) && d.tags.length ? c.addClass("annotator-tags").html(function () {
                var c;
                return c = a.map(d.tags, function (a) {
                    return '<span class="annotator-tag">' + b.$.escape(a) + "</span>"
                }).join(" ")
            }) : c.remove()
        }, d
    }(b.Plugin), b.Plugin.Tags.filterCallback = function (a, b) {
        var c, d, e, f, g, h, i, j;
        b == null && (b = []), e = 0, d = [];
        if (a) {
            d = a.split(/\s+/g);
            for (g = 0, i = d.length; g < i; g++) {
                c = d[g];
                if (b.length) for (h = 0, j = b.length; h < j; h++) f = b[h], f.indexOf(c) !== -1 && (e += 1)
            }
        }
        return e === d.length
    }, h = function (a) {
        var b, c, d, e, f, g;
        return e = "([0-9]{4})(-([0-9]{2})(-([0-9]{2})(T([0-9]{2}):([0-9]{2})(:([0-9]{2})(.([0-9]+))?)?(Z|(([-+])([0-9]{2}):([0-9]{2})))?)?)?)?", b = a.match(new RegExp(e)), d = 0, c = new Date(b[1], 0, 1), b[3] && c.setMonth(b[3] - 1), b[5] && c.setDate(b[5]), b[7] && c.setHours(b[7]), b[8] && c.setMinutes(b[8]), b[10] && c.setSeconds(b[10]), b[12] && c.setMilliseconds(Number("0." + b[12]) * 1e3), b[14] && (d = Number(b[16]) * 60 + Number(b[17]), d *= (g = b[15] === "-") != null ? g : {
            1: -1
        }), d -= c.getTimezoneOffset(), f = Number(c) + d * 60 * 1e3, c.setTime(Number(f)), c
    }, f = function (a) {
        var b, c, d, e, f, g, h, i, j, k, l, m, n;
        if (typeof atob != "undefined" && atob !== null) return atob(a);
        c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", j = 0, b = 0, e = "", n = [];
        if (!a) return a;
        a += "";
        while (j < a.length) f = c.indexOf(a.charAt(j++)), g = c.indexOf(a.charAt(j++)), h = c.indexOf(a.charAt(j++)), i = c.indexOf(a.charAt(j++)), d = f << 18 | g << 12 | h << 6 | i, k = d >> 16 & 255, l = d >> 8 & 255, m = d & 255, h === 64 ? n[b++] = String.fromCharCode(k) : i === 64 ? n[b++] = String.fromCharCode(k, l) : n[b++] = String.fromCharCode(k, l, m);
        return n.join("")
    }, g = function (a) {
        var b, c, d;
        c = a.length % 4;
        if (c !== 0) for (b = 0, d = 4 - c; 0 <= d ? b < d : b > d; 0 <= d ? b++ : b--) a += "=";
        return a = a.replace(/-/g, "+"), a = a.replace(/_/g, "/"), f(a)
    }, l = function (a) {
        var b, c, d, e;
        return e = a.split("."), b = e[0], c = e[1], d = e[2], JSON.parse(g(c))
    }, b.Plugin.Auth = function (c) {
        function d(a, b) {
            d.__super__.constructor.apply(this, arguments), this.waitingForToken = [], this.options.token ? this.setToken(this.options.token) : this.requestToken()
        }
        return y(d, c), d.prototype.options = {
            token: null,
            tokenUrl: "/auth/token",
            autoFetch: !0
        }, d.prototype.requestToken = function () {
            var c = this;
            return this.requestInProgress = !0, a.ajax({
                url: this.options.tokenUrl,
                dataType: "text",
                xhrFields: {
                    withCredentials: !0
                }
            }).done(function (a, b, d) {
                return c.setToken(a)
            }).fail(function (a, c, d) {
                var e;
                return e = b._t("Couldn't get auth token:"), console.error("" + e + " " + d, a), b.showNotification("" + e + " " + a.responseText, b.Notification.ERROR)
            }).always(function () {
                return c.requestInProgress = !1
            })
        }, d.prototype.setToken = function (a) {
            var c, d = this;
            this.token = a, this._unsafeToken = l(a);
            if (this.haveValidToken()) {
                this.options.autoFetch && (this.refreshTimeout = setTimeout(function () {
                    return d.requestToken()
                }, (this.timeToExpiry() - 2) * 1e3)), this.updateHeaders(), c = [];
                while (this.waitingForToken.length > 0) c.push(this.waitingForToken.pop()(this._unsafeToken));
                return c
            }
            console.warn(b._t("Didn't get a valid token."));
            if (this.options.autoFetch) return console.warn(b._t("Getting a new token in 10s.")), setTimeout(function () {
                return d.requestToken()
            }, 1e4)
        }, d.prototype.haveValidToken = function () {
            var a;
            return a = this._unsafeToken && this._unsafeToken.issuedAt && this._unsafeToken.ttl && this._unsafeToken.consumerKey, a && this.timeToExpiry() > 0
        }, d.prototype.timeToExpiry = function () {
            var a, b, c, d;
            return c = (new Date).getTime() / 1e3, b = h(this._unsafeToken.issuedAt).getTime() / 1e3, a = b + this._unsafeToken.ttl, d = a - c, d > 0 ? d : 0
        }, d.prototype.updateHeaders = function () {
            var b;
            return b = this.element.data("annotator:headers"), this.element.data("annotator:headers", a.extend(b, {
                "x-annotator-auth-token": this.token
            }))
        }, d.prototype.withToken = function (a) {
            if (a == null) return;
            if (this.haveValidToken()) return a(this._unsafeToken);
            this.waitingForToken.push(a);
            if (!this.requestInProgress) return this.requestToken()
        }, d
    }(b.Plugin), b.Plugin.Store = function (c) {
        function d(a, b) {
            this._onError = x(this._onError, this), this._onLoadAnnotationsFromSearch = x(this._onLoadAnnotationsFromSearch, this), this._onLoadAnnotations = x(this._onLoadAnnotations, this), this._getAnnotations = x(this._getAnnotations, this), d.__super__.constructor.apply(this, arguments), this.annotations = []
        }
        return y(d, c), d.prototype.events = {
            annotationCreated: "annotationCreated",
            annotationDeleted: "annotationDeleted",
            annotationUpdated: "annotationUpdated"
        }, d.prototype.options = {
            prefix: "/store",
            autoFetch: !0,
            annotationData: {},
            loadFromSearch: !1,
            urls: {
                create: "/annotations",
                read: "/annotations/:id",
                update: "/annotations/:id",
                destroy: "/annotations/:id",
                search: "/search"
            }
        }, d.prototype.pluginInit = function () {
            if (!b.supported()) return;
            return this.annotator.plugins.Auth ? this.annotator.plugins.Auth.withToken(this._getAnnotations) : this._getAnnotations()
        }, d.prototype._getAnnotations = function () {
            return this.options.loadFromSearch ? this.loadAnnotationsFromSearch(this.options.loadFromSearch) : this.loadAnnotations()
        }, d.prototype.annotationCreated = function (a) {
            var c = this;
            return z.call(this.annotations, a) < 0 ? (this.registerAnnotation(a), this._apiRequest("create", a, function (d) {
                return d.id == null && console.warn(b._t("Warning: No ID returned from server for annotation "), a), c.updateAnnotation(a, d)
            })) : this.updateAnnotation(a, {})
        }, d.prototype.annotationUpdated = function (a) {
            var b = this;
            if (z.call(this.annotations, a) >= 0) return this._apiRequest("update", a, function (c) {
                return b.updateAnnotation(a, c)
            })
        }, d.prototype.annotationDeleted = function (a) {
            var b = this;
            if (z.call(this.annotations, a) >= 0) return this._apiRequest("destroy", a, function () {
                return b.unregisterAnnotation(a)
            })
        }, d.prototype.registerAnnotation = function (a) {
            return this.annotations.push(a)
        }, d.prototype.unregisterAnnotation = function (a) {
            return this.annotations.splice(this.annotations.indexOf(a), 1)
        }, d.prototype.updateAnnotation = function (c, d) {
            return z.call(this.annotations, c) < 0 ? console.error(b._t("Trying to update unregistered annotation!")) : a.extend(c, d), a(c.highlights).data("annotation", c)
        }, d.prototype.loadAnnotations = function () {
            return this._apiRequest("read", null, this._onLoadAnnotations)
        }, d.prototype._onLoadAnnotations = function (a) {
	        a = a.rows;
            return a == null && (a = []), this.annotations = a, this.annotator.loadAnnotations(a.slice())
        }, d.prototype.loadAnnotationsFromSearch = function (a) {
            return this._apiRequest("search", a, this._onLoadAnnotationsFromSearch)
        }, d.prototype._onLoadAnnotationsFromSearch = function (a) {
            return a == null && (a = {}), this._onLoadAnnotations(a.rows || [])
        }, d.prototype.dumpAnnotations = function () {
            var a, b, c, d, e;
            d = this.annotations, e = [];
            for (b = 0, c = d.length; b < c; b++) a = d[b], e.push(JSON.parse(this._dataFor(a)));
            return e
        }, d.prototype._apiRequest = function (b, c, d) {
            var e, f, g, h;
            return e = c && c.id, h = this._urlFor(b, e), f = this._apiRequestOptions(b, c, d), g = a.ajax(h, f), g._id = e, g._action = b, g
        }, d.prototype._apiRequestOptions = function (b, c, d) {
            var e;
            return e = {
                type: this._methodFor(b),
                headers: this.element.data("annotator:headers"),
                dataType: "json",
                success: d || function () {},
                error: this._onError
            }, b === "search" ? e = a.extend(e, {
                data: c
            }) : e = a.extend(e, {
                data: c && this._dataFor(c),
                contentType: "application/json; charset=utf-8"
            }), e
        }, d.prototype._urlFor = function (a, b) {
            var c, d;
            return c = b != null ? "/" + b : "", d = this.options.prefix || "/", d += this.options.urls[a], d = d.replace(/\/:id/, c), d
        }, d.prototype._methodFor = function (a) {
            var b;
            return b = {
                create: "POST",
                read: "GET",
                update: "PUT",
                destroy: "DELETE",
                search: "GET"
            }, b[a]
        }, d.prototype._dataFor = function (b) {
            var c, d;
            return d = b.highlights, delete b.highlights, a.extend(b, this.options.annotationData), c = JSON.stringify(b), d && (b.highlights = d), c
        }, d.prototype._onError = function (a) {
            var c, d;
            c = a._action, d = b._t("Sorry we could not ") + c + b._t(" this annotation"), a._action === "search" ? d = b._t("Sorry we could not search the store for annotations") : a._action === "read" && !a._id && (d = b._t("Sorry we could not ") + c + b._t(" the annotations from the store"));
            switch (a.status) {
                case 401:
                    d = b._t("Sorry you are not allowed to ") + c + b._t(" this annotation");
                    break;
                case 404:
                    d = b._t("Sorry we could not connect to the annotations store");
                    break;
                case 500:
                    d = b._t("Sorry something went wrong with the annotation store")
            }
            return b.showNotification(d, b.Notification.ERROR), console.error(b._t("API request failed:") + (" '" + a.status + "'"))
        }, d
    }(b.Plugin), b.Plugin.Filter = function (c) {
        function d(b, c) {
            this._onPreviousClick = x(this._onPreviousClick, this), this._onNextClick = x(this._onNextClick, this), this._onFilterKeyup = x(this._onFilterKeyup, this), this._onFilterBlur = x(this._onFilterBlur, this), this._onFilterFocus = x(this._onFilterFocus, this), this.updateHighlights = x(this.updateHighlights, this), b = a(this.html.element).appendTo(this.options.appendTo), d.__super__.constructor.call(this, b, c), this.filter = a(this.html.filter), this.filters = [], this.current = 0
        }
        return y(d, c), d.prototype.events = {
            ".annotator-filter-property input focus": "_onFilterFocus",
            ".annotator-filter-property input blur": "_onFilterBlur",
            ".annotator-filter-property input keyup": "_onFilterKeyup",
            ".annotator-filter-previous click": "_onPreviousClick",
            ".annotator-filter-next click": "_onNextClick",
            ".annotator-filter-clear click": "_onClearClick"
        }, d.prototype.classes = {
            active: "annotator-filter-active",
            hl: {
                hide: "annotator-hl-filtered",
                active: "annotator-hl-active"
            }
        }, d.prototype.html = {
            element: '<div class="annotator-filter">\n  <strong>' + b._t("Navigate:") + '</strong>\n<span class="annotator-filter-navigation">\n  <button class="annotator-filter-previous">' + b._t("Previous") + '</button>\n<button class="annotator-filter-next">' + b._t("Next") + "</button>\n</span>\n<strong>" + b._t("Filter by:") + "</strong>\n</div>",
            filter: '<span class="annotator-filter-property">\n  <label></label>\n  <input/>\n  <button class="annotator-filter-clear">' + b._t("Clear") + "</button>\n</span>"
        }, d.prototype.options = {
            appendTo: "body",
            filters: [],
            addAnnotationFilter: !0,
            isFiltered: function (a, b) {
                var c, d, e, f;
                if (!a || !b) return !1;
                f = a.split(/\s*/);
                for (d = 0, e = f.length; d < e; d++) {
                    c = f[d];
                    if (b.indexOf(c) === -1) return !1
                }
                return !0
            }
        }, d.prototype.pluginInit = function () {
            var a, c, d, e;
            e = this.options.filters;
            for (c = 0, d = e.length; c < d; c++) a = e[c], this.addFilter(a);
            this.updateHighlights(), this._setupListeners()._insertSpacer();
            if (this.options.addAnnotationFilter === !0) return this.addFilter({
                label: b._t("Annotation"),
                property: "text"
            })
        }, d.prototype._insertSpacer = function () {
            var b, c;
            return c = a("html"), b = parseInt(c.css("padding-top"), 10) || 0, c.css("padding-top", b + this.element.outerHeight()), this
        }, d.prototype._setupListeners = function () {
            var a, b, c, d;
            b = ["annotationsLoaded", "annotationCreated", "annotationUpdated", "annotationDeleted"];
            for (c = 0, d = b.length; c < d; c++) a = b[c], this.annotator.subscribe(a, this.updateHighlights);
            return this
        }, d.prototype.addFilter = function (c) {
            var d, e;
            e = a.extend({
                label: "",
                property: "",
                isFiltered: this.options.isFiltered
            }, c);
            if (! function () {
                var a, b, c, f;
                c = this.filters, f = [];
                for (a = 0, b = c.length; a < b; a++) d = c[a], d.property === e.property && f.push(d);
                return f
            }.call(this).length) e.id = "annotator-filter-" + e.property, e.annotations = [], e.element = this.filter.clone().appendTo(this.element), e.element.find("label").html(e.label).attr("for", e.id), e.element.find("input").attr({
                id: e.id,
                placeholder: b._t("Filter by ") + e.label + "…"
            }), e.element.find("button").hide(), e.element.data("filter", e), this.filters.push(e);
            return this
        }, d.prototype.updateFilter = function (b) {
            var c, d, e, f, g, h, i;
            b.annotations = [], this.updateHighlights(), this.resetHighlights(), e = a.trim(b.element.find("input").val());
            if (e) {
                d = this.highlights.map(function () {
                    return a(this).data("annotation")
                }), i = a.makeArray(d);
                for (g = 0, h = i.length; g < h; g++) c = i[g], f = c[b.property], b.isFiltered(e, f) && b.annotations.push(c);
                return this.filterHighlights()
            }
        }, d.prototype.updateHighlights = function () {
            return this.highlights = this.annotator.element.find(".annotator-hl:visible"), this.filtered = this.highlights.not(this.classes.hl.hide)
        }, d.prototype.filterHighlights = function () {
            var b, c, d, e, f, g, h, i, j;
            b = a.grep(this.filters, function (a) {
                return !!a.annotations.length
            }), e = ((j = b[0]) != null ? j.annotations : void 0) || [], b.length > 1 && (d = [], a.each(b, function () {
                return a.merge(d, this.annotations)
            }), h = [], e = [], a.each(d, function () {
                return a.inArray(this, h) === -1 ? h.push(this) : e.push(this)
            })), f = this.highlights;
            for (g = 0, i = e.length; g < i; g++) c = e[g], f = f.not(c.highlights);
            return f.addClass(this.classes.hl.hide), this.filtered = this.highlights.not(this.classes.hl.hide), this
        }, d.prototype.resetHighlights = function () {
            return this.highlights.removeClass(this.classes.hl.hide), this.filtered = this.highlights, this
        }, d.prototype._onFilterFocus = function (b) {
            var c;
            return c = a(b.target), c.parent().addClass(this.classes.active), c.next("button").show()
        }, d.prototype._onFilterBlur = function (b) {
            var c;
            if (!b.target.value) return c = a(b.target), c.parent().removeClass(this.classes.active), c.next("button").hide()
        }, d.prototype._onFilterKeyup = function (b) {
            var c;
            c = a(b.target).parent().data("filter");
            if (c) return this.updateFilter(c)
        }, d.prototype._findNextHighlight = function (a) {
            var b, c, d, e, f, g, h, i;
            return this.highlights.length ? (g = a ? 0 : -1, i = a ? -1 : 0, h = a ? "lt" : "gt", b = this.highlights.not("." + this.classes.hl.hide), d = b.filter("." + this.classes.hl.active), d.length || (d = b.eq(g)), c = d.data("annotation"), e = b.index(d[0]), f = b.filter(":" + h + "(" + e + ")").not(c.highlights).eq(i), f.length || (f = b.eq(i)), this._scrollToHighlight(f.data("annotation").highlights)) : this
        }, d.prototype._onNextClick = function (a) {
            return this._findNextHighlight()
        }, d.prototype._onPreviousClick = function (a) {
            return this._findNextHighlight(!0)
        }, d.prototype._scrollToHighlight = function (b) {
            return b = a(b), this.highlights.removeClass(this.classes.hl.active), b.addClass(this.classes.hl.active), a("html, body").animate({
                scrollTop: b.offset().top - (this.element.height() + 20)
            }, 150)
        }, d.prototype._onClearClick = function (b) {
            return a(b.target).prev("input").val("").keyup().blur()
        }, d
    }(b.Plugin), b.Plugin.Markdown = function (c) {
        function d(a, c) {
            this.updateTextField = x(this.updateTextField, this), (typeof Showdown !== "undefined" && Showdown !== null ? Showdown.converter : void 0) != null ? (d.__super__.constructor.apply(this, arguments), this.converter = new Showdown.converter) : console.error(b._t("To use the Markdown plugin, you must include Showdown into the page first."))
        }
        return y(d, c), d.prototype.events = {
            annotationViewerTextField: "updateTextField"
        }, d.prototype.updateTextField = function (c, d) {
            var e;
            return e = b.$.escape(d.text || ""), a(c).html(this.convert(e))
        }, d.prototype.convert = function (a) {
            return this.converter.makeHtml(a)
        }, d
    }(b.Plugin), b.Plugin.Unsupported = function (c) {
        function d() {
            d.__super__.constructor.apply(this, arguments)
        }
        return y(d, c), d.prototype.options = {
            message: b._t("Sorry your current browser does not support the Annotator")
        }, d.prototype.pluginInit = function () {
            var c = this;
            if (!b.supported()) return a(function () {
                b.showNotification(c.options.message);
                if (window.XMLHttpRequest === void 0 && ActiveXObject !== void 0) return a("html").addClass("ie6")
            })
        }, d
    }(b.Plugin), b.Plugin.Permissions = function (c) {
        function d(a, b) {
            this._setAuthFromToken = x(this._setAuthFromToken, this), this.updateViewer = x(this.updateViewer, this), this.updateAnnotationPermissions = x(this.updateAnnotationPermissions, this), this.updatePermissionsField = x(this.updatePermissionsField, this), this.addFieldsToAnnotation = x(this.addFieldsToAnnotation, this), d.__super__.constructor.apply(this, arguments), this.options.user && (this.setUser(this.options.user), delete this.options.user)
        }
        return y(d, c), d.prototype.events = {
            beforeAnnotationCreated: "addFieldsToAnnotation"
        }, d.prototype.options = {
            showViewPermissionsCheckbox: !0,
            showEditPermissionsCheckbox: !0,
            userId: function (a) {
                return a
            },
            userString: function (a) {
                return a
            },
            userAuthorize: function (a, b, c) {
                var d, e, f, g;
                if (b.permissions) {
                    e = b.permissions[a] || [];
                    if (e.length === 0) return !0;
                    for (f = 0, g = e.length; f < g; f++) {
                        d = e[f];
                        if (this.userId(c) === d) return !0
                    }
                    return !1
                }
                return b.user ? c && this.userId(c) === this.userId(b.user) : !0
            },
            user: "",
            permissions: {
                read: [],
                update: [],
                "delete": [],
                admin: []
            }
        }, d.prototype.pluginInit = function () {
            var a, c, d = this;
            if (!b.supported()) return;
            c = this, a = function (a, b) {
                return function (d, e) {
                    return c[a].call(c, b, d, e)
                }
            }, !this.user && this.annotator.plugins.Auth && this.annotator.plugins.Auth.withToken(this._setAuthFromToken), this.options.showViewPermissionsCheckbox === !0 && this.annotator.editor.addField({
                type: "checkbox",
                label: b._t("Allow anyone to <strong>view</strong> this annotation"),
                load: a("updatePermissionsField", "read"),
                submit: a("updateAnnotationPermissions", "read")
            }), this.options.showEditPermissionsCheckbox === !0 && this.annotator.editor.addField({
                type: "checkbox",
                label: b._t("Allow anyone to <strong>edit</strong> this annotation"),
                load: a("updatePermissionsField", "update"),
                submit: a("updateAnnotationPermissions", "update")
            }), this.annotator.viewer.addField({
                load: this.updateViewer
            });
            if (this.annotator.plugins.Filter) return this.annotator.plugins.Filter.addFilter({
                label: b._t("User"),
                property: "user",
                isFiltered: function (a, b) {
                    var c, e, f, g;
                    b = d.options.userString(b);
                    if (!a || !b) return !1;
                    g = a.split(/\s*/);
                    for (e = 0, f = g.length; e < f; e++) {
                        c = g[e];
                        if (b.indexOf(c) === -1) return !1
                    }
                    return !0
                }
            })
        }, d.prototype.setUser = function (a) {
            return this.user = a
        }, d.prototype.addFieldsToAnnotation = function (a) {
            if (a) {
                a.permissions = this.options.permissions;
                if (this.user) return a.user = this.user
            }
        }, d.prototype.authorize = function (a, b, c) {
            return c === void 0 && (c = this.user), this.options.userAuthorize ? this.options.userAuthorize.call(this.options, a, b, c) : !0
        }, d.prototype.updatePermissionsField = function (b, c, d) {
            var e;
            return c = a(c).show(), e = c.find("input").removeAttr("disabled"), this.authorize("admin", d) || c.hide(), this.authorize(b, d || {}, null) ? e.attr("checked", "checked") : e.removeAttr("checked")
        }, d.prototype.updateAnnotationPermissions = function (b, c, d) {
            var e;
            return d.permissions || (d.permissions = this.options.permissions), e = b + "-permissions", a(c).find("input").is(":checked") ? d.permissions[b] = [] : d.permissions[b] = [this.user]
        }, d.prototype.updateViewer = function (c, d, e) {
            var f, g;
            c = a(c), g = this.options.userString(d.user), d.user && g && typeof g == "string" ? (f = b.$.escape(this.options.userString(d.user)), c.html(f).addClass("annotator-user")) : c.remove(), this.authorize("update", d) || e.hideEdit();
            if (!this.authorize("delete", d)) return e.hideDelete()
        }, d.prototype._setAuthFromToken = function (a) {
            return this.setUser(a.userId)
        }, d
    }(b.Plugin), b.Plugin.AnnotateItPermissions = function (b) {
        function c(a, b) {
            this._setAuthFromToken = x(this._setAuthFromToken, this), this.updateAnnotationPermissions = x(this.updateAnnotationPermissions, this), this.updatePermissionsField = x(this.updatePermissionsField, this), this.addFieldsToAnnotation = x(this.addFieldsToAnnotation, this), c.__super__.constructor.apply(this, arguments), this.options.consumer && (this.setConsumer(this.options.consumer), delete this.options.consumer)
        }
        return y(c, b), c.prototype.options = {
            showViewPermissionsCheckbox: !0,
            showEditPermissionsCheckbox: !0,
            groups: {
                world: "group:__world__",
                authenticated: "group:__authenticated__",
                consumer: "group:__consumer__"
            },
            userString: function (a) {
                return a
            },
            userAuthorize: function (a, b, c, d) {
                var e, f, g, h, i, j;
                return f = b.permissions || {}, e = f[a] || [], !c || !d ? (g = this.groups.world, z.call(e, g) >= 0) : (h = this.groups.world, z.call(e, h) >= 0) ? !0 : c === b.user && d === b.consumer ? !0 : (i = this.groups.authenticated, z.call(e, i) >= 0) ? !0 : d === b.consumer && (j = this.groups.consumer, z.call(e, j) >= 0) ? !0 : d === b.consumer && z.call(e, c) >= 0 ? !0 : !1
            },
            user: "",
            consumer: "annotateit",
            permissions: {
                read: ["group:__world__"],
                update: [],
                "delete": [],
                admin: []
            }
        }, c.prototype.setConsumer = function (a) {
            return this.consumer = a
        }, c.prototype.authorize = function (a, b, c, d) {
            return c === void 0 && (c = this.user), d === void 0 && (d = this.consumer), this.options.userAuthorize ? this.options.userAuthorize.call(this.options, a, b, c, d) : !0
        }, c.prototype.addFieldsToAnnotation = function (a) {
            c.__super__.addFieldsToAnnotation.apply(this, arguments);
            if (a && this.consumer) return a.consumer = this.consumer
        }, c.prototype.updatePermissionsField = function (b, c, d) {
            var e;
            return c = a(c).show(), e = c.find("input").removeAttr("disabled"), this.authorize("admin", d) || c.hide(), this.authorize(b, d || {}, "__nonexistentuser__") ? e.attr("checked", "checked") : e.removeAttr("checked")
        }, c.prototype.updateAnnotationPermissions = function (b, c, d) {
            var e;
            return d.permissions || (d.permissions = this.options.permissions), e = b + "-permissions", a(c).find("input").is(":checked") ? d.permissions[b] = [b === "read" ? this.options.groups.world : this.options.groups.consumer] : d.permissions[b] = []
        }, c.prototype._setAuthFromToken = function (a) {
            return c.__super__._setAuthFromToken.apply(this, arguments), this.setConsumer(a.consumerKey)
        }, c
    }(b.Plugin.Permissions), b.prototype.setupPlugins = function (c, d) {
        var e, f, g, h, i, j, k, l, n;
        c == null && (c = {}), d == null && (d = {}), j = m.getGlobal(), h = ["Unsupported", "Auth", "Tags", "Filter", "Store", "Permissions"], j.Showdown && h.push("Markdown"), i = j.location.href.split(/#|\?/).shift() || "", g = {
            Tags: {},
            Filter: {
                filters: [{
                    label: b._t("User"),
                    property: "user"
                }, {
                    label: b._t("Tags"),
                    property: "tags"
                }]
            },
            Auth: {
                tokenUrl: c.tokenUrl || "http://annotateit.org/api/token"
            },
            Store: {
                prefix: c.storeUrl || "http://annotateit.org/api",
                annotationData: {
                    uri: i
                },
                loadFromSearch: {
                    uri: i
                }
            }
        };
        for (e in d) {
            if (!w.call(d, e)) continue;
            f = d[e], z.call(h, e) < 0 && h.push(e)
        }
        a.extend(!0, g, d), n = [];
        for (k = 0, l = h.length; k < l; k++) e = h[k], e in g && !g[e] ? n.push(void 0) : n.push(this.addPlugin(e, g[e]));
        return n
    }
})).call(this);