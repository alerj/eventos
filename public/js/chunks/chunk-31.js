(window.webpackJsonp=window.webpackJsonp||[]).push([[31],{Aqzt:function(t,e,n){"use strict";var r=n("L2JU");function s(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{},r=Object.keys(n);"function"==typeof Object.getOwnPropertySymbols&&(r=r.concat(Object.getOwnPropertySymbols(n).filter(function(t){return Object.getOwnPropertyDescriptor(n,t).enumerable}))),r.forEach(function(e){a(t,e,n[e])})}return t}function a(t,e,n){return e in t?Object.defineProperty(t,e,{value:n,enumerable:!0,configurable:!0,writable:!0}):t[e]=n,t}e.a={methods:s({},Object(r.mapActions)("topics",["clearForm"])),computed:s({},Object(r.mapState)({topics:function(t){return t.topics}}))}},enpM:function(t,e,n){"use strict";n.r(e);var r=n("jx1L"),s=n("Aqzt"),a={name:"topics",uri:"topics",performLoad:!1},o={props:["mode"],mixins:[r.a,s.a],data:function(){return{service:a}}},i=n("KHd+"),c=Object(i.a)(o,function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("div",{staticClass:"py-2 text-center"},[n("h2",[t._v("\n            "+t._s("create"===this.mode?"Criar ":"Editar ")+"Interesses\n        ")]),t._v(" "),n("h2",[t._v(" "+t._s(t.form.fields.name?t.form.fields.name:""))])]),t._v(" "),n("div",{staticClass:"row justify-content-center"},[n("div",{staticClass:"col-8"},[n("form",[n("div",{staticClass:"row"},[n("div",{staticClass:"col-12 mb-3"},[n("app-input",{attrs:{name:"name",label:"Nome",required:!0,form:t.form},model:{value:t.form.fields.name,callback:function(e){t.$set(t.form.fields,"name",e)},expression:"form.fields.name"}})],1)]),t._v(" "),n("div",{staticClass:"row"},[n("div",{staticClass:"col-12 text-right mb-3"},[n("button",{staticClass:"btn btn-outline-secondary",attrs:{type:"submit"},on:{click:function(e){return e.preventDefault(),t.saveModel()}}},[t._v("\n                            gravar\n                        ")]),t._v(" "),n("router-link",{staticClass:"btn btn-success",attrs:{to:"/topics",tag:"button"}},[t._v("\n                            cancelar\n                        ")])],1)])])])])])},[],!1,null,null,null);e.default=c.exports}}]);