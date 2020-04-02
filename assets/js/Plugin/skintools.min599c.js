/*!
 * Remark (http://getbootstrapadmin.com/remark)
 * Copyright 2017 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */

! function (global, factory) {
    if ("function" == typeof define && define.amd) define("/Plugin/skintools", [], factory);
    else if ("undefined" != typeof exports) factory();
    else {
        var mod = {
            exports: {}
        };
        factory(), global.PluginSkintools = mod.exports
    }
}(this, function () {
    "use strict";
    if (window.localStorage) {
        var getLevel = function (url, tag) {
            for (var arr = url.split("/").reverse(), level = void 0, path = "", i = 0; i < arr.length; i++) arr[i] === tag && (level = i);
            for (var m = 1; m < level; m++) path += "../";
            return path
        },
            settings = localStorage.getItem("remark.topbar.skinTools");
        if (settings) {
            if ("{" === settings[0] && (settings = JSON.parse(settings)), settings.primary && "primary" !== settings.primary) {
                var head = document.head,
                    link = document.createElement("link");
                link.type = "text/css", link.rel = "stylesheet", link.href = getLevel(window.location.pathname, "topbar") + "assets/skins/" + settings.primary + ".css", link.id = "skinStyle", head.appendChild(link)
            }
            if (settings.sidebar && "light" === settings.sidebar) var menubarFn = setInterval(function () {
                var menubar = document.getElementsByClassName("site-menubar");
                menubar.length > 0 && (clearInterval(menubarFn), menubar[0].className += " site-menubar-light")
            }, 5);
            var navbarFn = setInterval(function () {
                var navbar = document.getElementsByClassName("site-navbar");
                navbar.length > 0 && (clearInterval(navbarFn), settings.navbarInverse && "false" !== settings.navbarInverse && (navbar[0].className += " navbar-inverse"))
            }, 5)
        }
        document.addEventListener && document.addEventListener("DOMContentLoaded", function () {
            var $body = $(document.body),
                Storage = ($(document), $(window), {
                    set: function (key, value) {
                        return window.localStorage && key && value ? (Object(value) === value && (value = JSON.stringify(value)), void localStorage.setItem(key, value)) : null
                    },
                    get: function (key) {
                        if (!window.localStorage) return null;
                        var value = localStorage.getItem(key);
                        return value ? ("{" === value[0] && (value = JSON.parse(value)), value) : null
                    }
                });
            ({
                tpl: '<div class="site-skintools"><div class="site-skintools-inner"><div class="site-skintools-toggle"><i class="icon wb-settings primary-600"></i></div><div class="site-skintools-content"><div class="nav-tabs-horizontal"><ul role="tablist" class="nav nav-tabs nav-tabs-line"><li role="presentation" class="nav-item"><a class="nav-link active" role="tab" aria-controls="skintoolsSidebar" href="#skintoolsSidebar" data-toggle="tab" aria-expanded="true">Sidebar</a></li><li class="nav-item" role="presentation"><a class="nav-link" role="tab" aria-controls="skintoolsNavbar" href="#skintoolsNavbar" data-toggle="tab" aria-expanded="false">Navbar</a></li><li class="nav-item" role="presentation"><a class="nav-link" role="tab" aria-controls="skintoolsPrimary" href="#skintoolsPrimary" data-toggle="tab" aria-expanded="false">Primary</a></li></ul><div class="tab-content"><div role="tabpanel" id="skintoolsSidebar" class="tab-pane active"></div><div role="tabpanel" id="skintoolsNavbar" class="tab-pane"></div><div role="tabpanel" id="skintoolsPrimary" class="tab-pane"></div><button class="btn btn-outline btn-block btn-primary mt-20" id="skintoolsReset" type="button">Reset</button></div></div></div></div></div>',
                skintoolsSidebar: ["dark", "light"],
                skintoolsNavbar: ["primary", "brown", "cyan", "green", "grey", "indigo", "orange", "pink", "purple", "red", "teal", "yellow"],
                navbarSkins: "bg-primary-600 bg-brown-600 bg-cyan-600 bg-green-600 bg-grey-600 bg-indigo-600 bg-orange-600 bg-pink-600 bg-purple-600 bg-red-600 bg-teal-600 bg-yellow-700",
                skintoolsPrimary: ["primary", "brown", "cyan", "green", "grey", "indigo", "orange", "pink", "purple", "red", "teal", "yellow"],
                storageKey: "remark.topbar.skinTools",
                defaultSettings: {
                    sidebar: "light",
                    navbar: "primary",
                    navbarInverse: "true",
                    primary: "primary"
                },
                init: function () {
                    var self = this;
                    this.path = getLevel(window.location.pathname, "topbar"), this.overflow = !1, this.$siteSidebar = $(".site-menubar"), this.$siteNavbar = $(".site-navbar"), this.$container = $(this.tpl), this.$toggle = $(".site-skintools-toggle", this.$container), this.$content = $(".site-skintools-content", this.$container), this.$tabContent = $(".tab-content", this.$container), this.$sidebar = $("#skintoolsSidebar", this.$content), this.$navbar = $("#skintoolsNavbar", this.$content), this.$primary = $("#skintoolsPrimary", this.$content), this.build(this.$sidebar, this.skintoolsSidebar, "skintoolsSidebar", "radio", "Sidebar Skins"), this.build(this.$navbar, ["inverse"], "skintoolsNavbar", "checkbox", "Navbar Type"), this.build(this.$navbar, this.skintoolsNavbar, "skintoolsNavbar", "radio", "Navbar Skins"), this.build(this.$primary, this.skintoolsPrimary, "skintoolsPrimary", "radio", "Primary Skins"), this.$container.appendTo($body), this.$toggle.on("click", function () {
                        self.$container.toggleClass("is-open")
                    }), $("#skintoolsSidebar input").on("click", function () {
                        self.sidebarEvents(this)
                    }), $("#skintoolsNavbar input").on("click", function () {
                        self.navbarEvents(this)
                    }), $("#skintoolsPrimary input").on("click", function () {
                        self.primaryEvents(this)
                    }), $("#skintoolsReset").on("click", function () {
                        self.reset()
                    }), this.initLocalStorage()
                },
                initLocalStorage: function () {
                    var self = this;
                    this.settings = Storage.get(this.storageKey), null === this.settings && (this.settings = $.extend(!0, {}, this.defaultSettings), Storage.set(this.storageKey, this.settings)), this.settings && $.isPlainObject(this.settings) && $.each(this.settings, function (n, v) {
                        switch (n) {
                            case "sidebar":
                                $('input[value="' + v + '"]', self.$sidebar).prop("checked", !0), self.sidebarImprove(v);
                                break;
                            case "navbar":
                                $('input[value="' + v + '"]', self.$navbar).prop("checked", !0), self.navbarImprove(v);
                                break;
                            case "navbarInverse":
                                var flag = "false" !== v;
                                $('input[value="inverse"]', self.$navbar).prop("checked", flag), self.navbarImprove("inverse", flag);
                                break;
                            case "primary":
                                $('input[value="' + v + '"]', self.$primary).prop("checked", !0), self.primaryImprove(v)
                        }
                    })
                },
                updateSetting: function (item, value) {
                    this.settings[item] = value, Storage.set(this.storageKey, this.settings)
                },
                title: function (content) {
                    return $('<h4 class="site-skintools-title">' + content + "</h4>")
                },
                item: function (type, name, id, content) {
                    var item = '<div class="' + type + "-custom " + type + "-" + content + '"><input id="' + id + '" type="' + type + '" name="' + name + '" value="' + content + '"><label for="' + id + '">' + content + "</label></div>";
                    return $(item)
                },
                build: function ($wrap, data, name, type, title) {
                    title && this.title(title).appendTo($wrap);
                    for (var i = 0; i < data.length; i++) this.item(type, name, name + "-" + data[i], data[i]).appendTo($wrap)
                },
                sidebarEvents: function (self) {
                    var val = $(self).val();
                    this.sidebarImprove(val), this.updateSetting("sidebar", val)
                },
                navbarEvents: function (self) {
                    var val = $(self).val(),
                        checked = $(self).prop("checked");
                    this.navbarImprove(val, checked), "inverse" === val ? this.updateSetting("navbarInverse", checked.toString()) : this.updateSetting("navbar", val)
                },
                primaryEvents: function (self) {
                    var val = $(self).val();
                    this.primaryImprove(val), this.updateSetting("primary", val)
                },
                sidebarImprove: function (val) {
                    this.$siteSidebar.removeClass("site-menubar-light"), "light" === val && this.$siteSidebar.addClass("site-menubar-" + val)
                },
                navbarImprove: function (val, checked) {
                    var change = function ($nav, value) {
                        var bg = "bg-" + value + "-600";
                        "yellow" === value && (bg = "bg-yellow-700"), "primary" === value && (bg = ""), $nav.addClass(bg)
                    };
                    "inverse" === val ? (checked ? this.$siteNavbar.addClass("navbar-inverse") : this.$siteNavbar.removeClass("navbar-inverse"), checked ? change(this.$siteNavbar, this.settings.navbar) : this.$siteNavbar.removeClass(this.navbarSkins)) : (this.$siteNavbar.removeClass(this.navbarSkins), "true" === this.settings.navbarInverse && change(this.$siteNavbar, val))
                },
                primaryImprove: function (val) {
                    var $link = $("#skinStyle", $("head")),
                        href = this.path + "assets/skins/" + val + ".css";
                    "primary" !== val ? 0 === $link.length ? $("head").append('<link id="skinStyle" href="' + href + '" rel="stylesheet" type="text/css"/>') : $link.attr("href", href) : $link.remove()
                },
                reset: function () {
                    localStorage.clear(), this.initLocalStorage()
                }
            }).init()
        })
    }
});