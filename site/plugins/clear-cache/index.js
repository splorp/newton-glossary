(()=>{(function(){"use strict";var f=function(){var e=this,n=e.$createElement,t=e._self._c||n;return t("k-inside",[t("k-view",{staticClass:"k-clearcache-view"},[t("k-header",[e._v("Clear cache "),t("small",[e._v("[temporary files and directories]")])]),t("k-info-field",{attrs:{text:"The files and folders listed here are temporary. It is recreated when deleted. There is no harm in deleting it."}}),t("k-field",{attrs:{label:"Cache",help:"All cached parts of your website are stored here."},scopedSlots:e._u([Object.keys(e.dirs.cache).length?{key:"options",fn:function(){return[t("k-button-group",{staticClass:"k-field-options"},[t("k-button",{attrs:{icon:"check"},on:{click:function(i){return e.clear("cache")}}},[e._v(" Clear all ")])],1)]},proxy:!0}:null],null,!0)},[Object.keys(e.dirs.cache).length?[t("k-items",{attrs:{items:e.dirs.cache,link:!1,layout:"list",sortable:"false"},scopedSlots:e._u([{key:"options",fn:function(i){var s=i.item;return[t("k-button",{attrs:{icon:"trash"},on:{click:function(l){return e.clear("cache",s.name)}}})]}}],null,!1,3274965701)})]:t("k-empty",{attrs:{icon:"check"}},[e._v(" No temporary files/directories yet ")])],2),t("k-field",{attrs:{label:"Media",help:"The panel folder is created immediately when you enter the panel or refresh the page after deleting it. Usually the version is cleared when the Kirby version is updated."},scopedSlots:e._u([Object.keys(e.dirs.media).length?{key:"options",fn:function(){return[t("k-button-group",{staticClass:"k-field-options"},[t("k-button",{attrs:{icon:"check"},on:{click:function(i){return e.clear("media")}}},[e._v("Clear all")])],1)]},proxy:!0}:null],null,!0)},[Object.keys(e.dirs.media).length?t("k-items",{attrs:{items:e.dirs.media,link:!1,layout:"list",sortable:"false"},scopedSlots:e._u([{key:"options",fn:function(i){var s=i.item;return[t("k-button",{attrs:{icon:"trash"},on:{click:function(l){return e.clear("media",s.name)}}})]}}],null,!1,2784937005)}):t("k-empty",{attrs:{icon:"check"}},[e._v(" No temporary files/directories yet ")])],1),t("k-field",{attrs:{label:"Other",help:"Other temporary files/directories of your website."},scopedSlots:e._u([Object.keys(e.dirs.other).length?{key:"options",fn:function(){return[t("k-button-group",{staticClass:"k-field-options"},[t("k-button",{attrs:{icon:"check"},on:{click:function(i){return e.clear("other")}}},[e._v("Clear all")])],1)]},proxy:!0}:null],null,!0)},[Object.keys(e.dirs.other).length?[t("k-items",{attrs:{items:e.dirs.other,link:!1,layout:"list",sortable:"false"},scopedSlots:e._u([{key:"options",fn:function(i){var s=i.item;return[t("k-button",{attrs:{icon:"trash"},on:{click:function(l){return e.clear("other",s.name)}}})]}}],null,!1,3592532941)})]:t("k-empty",{attrs:{icon:"check"}},[e._v(" No temporary files/directories yet ")])],2)],1)],1)},p=[],$="";function k(e,n,t,i,s,l,u,b){var r=typeof e=="function"?e.options:e;n&&(r.render=n,r.staticRenderFns=t,r._compiled=!0),i&&(r.functional=!0),l&&(r._scopeId="data-v-"+l);var a;if(u?(a=function(o){o=o||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext,!o&&typeof __VUE_SSR_CONTEXT__!="undefined"&&(o=__VUE_SSR_CONTEXT__),s&&s.call(this,o),o&&o._registeredComponents&&o._registeredComponents.add(u)},r._ssrRegister=a):s&&(a=b?function(){s.call(this,(r.functional?this.parent:this).$root.$options.shadowRoot)}:s),a)if(r.functional){r._injectStyles=a;var C=r.render;r.render=function(g,d){return a.call(d),C(g,d)}}else{var h=r.beforeCreate;r.beforeCreate=h?[].concat(h,a):[a]}return{exports:e,options:r}}const _={props:{cache:Array,media:Array,other:Array},data(){return{dirs:{cache:Object.values(this.cache),media:Object.values(this.media),other:Object.values(this.other)}}},methods:{async clear(e,n=null){try{await this.$api.post("clear-cache",{type:e,dir:n})&&(n?this.dirs[e]=this.dirs[e].filter(i=>i.name!==n):this.dirs[e]=[],e==="other"&&n==="lock"&&this.$store.dispatch("content/clear"),this.$store.dispatch("notification/success",":)"))}catch(t){throw t}}}},c={};var v=k(_,f,p,!1,m,null,null,null);function m(e){for(let n in c)this[n]=c[n]}var y=function(){return v.exports}();panel.plugin("clicktonext/clear-cache",{components:{clearcache:y}})})();})();