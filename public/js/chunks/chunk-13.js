(window.webpackJsonp=window.webpackJsonp||[]).push([[13],{CtO9:function(n,e,t){"use strict";e.a={methods:{can:function(){if(this.environment.user.hasOwnProperty("permissions"))for(var n=0;n<arguments.length;n++){if(JSON.parse(this.environment.user.permissions).hasOwnProperty(arguments[n]))return!0}return!1},cannot:function(n){return!this.can(n)}}}},LY3I:function(n,e,t){"use strict";var i=t("T9NN");t.n(i).a},"N4/z":function(n,e,t){"use strict";var i=t("L2JU");function s(n){for(var e=1;e<arguments.length;e++){var t=null!=arguments[e]?arguments[e]:{},i=Object.keys(t);"function"==typeof Object.getOwnPropertySymbols&&(i=i.concat(Object.getOwnPropertySymbols(t).filter(function(n){return Object.getOwnPropertyDescriptor(t,n).enumerable}))),i.forEach(function(e){r(n,e,t[e])})}return n}function r(n,e,t){return e in n?Object.defineProperty(n,e,{value:t,enumerable:!0,configurable:!0,writable:!0}):n[e]=t,n}e.a={methods:s({},Object(i.mapActions)("subEvents",["clearForm"])),computed:s({},Object(i.mapState)({addresses:function(n){return n.addresses},events:function(n){return n.events},subEvents:function(n){return n.subEvents}}))}},T9NN:function(n,e,t){var i=t("YuJv");"string"==typeof i&&(i=[[n.i,i,""]]);var s={hmr:!0,transform:void 0,insertInto:void 0};t("aET+")(i,s);i.locals&&(n.exports=i.locals)},YuJv:function(n,e,t){(n.exports=t("I1BE")(!1)).push([n.i,"\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n",""])},hsPB:function(n,e,t){"use strict";t.r(e);var i=t("jx1L"),s=t("N4/z"),r=t("L2JU");function o(n){for(var e=1;e<arguments.length;e++){var t=null!=arguments[e]?arguments[e]:{},i=Object.keys(t);"function"==typeof Object.getOwnPropertySymbols&&(i=i.concat(Object.getOwnPropertySymbols(t).filter(function(n){return Object.getOwnPropertyDescriptor(t,n).enumerable}))),i.forEach(function(e){a(n,e,t[e])})}return n}function a(n,e,t){return e in n?Object.defineProperty(n,e,{value:t,enumerable:!0,configurable:!0,writable:!0}):n[e]=t,n}var c={methods:o({},Object(r.mapActions)("invitables",["clearForm"])),computed:o({},Object(r.mapState)({invitables:function(n){return n.invitables}}))},l=t("CtO9");function u(n,e,t){return e in n?Object.defineProperty(n,e,{value:t,enumerable:!0,configurable:!0,writable:!0}):n[e]=t,n}var v={name:"invitables",uri:"events/{events.selected.id}/sub-events/{subEvents.selected.id}/invitations/invitables"},d={props:["mode"],mixins:[i.a,c,s.a,l.a],data:function(){return this.$store.dispatch("environment/loadSubEvents"),this.$store.dispatch("environment/loadRoles"),this.$store.dispatch("environment/loadInstitutions"),{service:v,checkedPeople:{}}},computed:function(n){for(var e=1;e<arguments.length;e++){var t=null!=arguments[e]?arguments[e]:{},i=Object.keys(t);"function"==typeof Object.getOwnPropertySymbols&&(i=i.concat(Object.getOwnPropertySymbols(t).filter(function(n){return Object.getOwnPropertyDescriptor(t,n).enumerable}))),i.forEach(function(e){u(n,e,t[e])})}return n}({},Object(r.mapState)({events:function(n){return n.events},subEvents:function(n){return n.subEvents}}),{notInvited:{get:function(){return this.$store.state.invitations.data.filter.checkboxes.not_invited},set:function(n){n?this.$store.commit("invitables/mutateFilterCheckbox",{field:"not_invited",value:this.subEvents.selected.id}):this.$store.commit("invitables/mutateFilterCheckbox",{field:"not_invited",value:null}),this.$store.dispatch("invitables/load")}},selectedSubEvent:{get:function(){return this.$store.state.invitables.data.filter.selects.sub_event}},recordButtonText:{get:function(){return this.selectedSubEvent?"copiar convidados":"convidar"}},invitablesFilterText:{get:function(){return this.$store.state.invitables.data.filter.text},set:function(n){return this.resetCheckedPeople(),this.$store.dispatch(this.service.name+"/mutateSetQueryFilterText",n)}},invitablesPerPage:{get:function(){return this.$store.state.invitables.data.links.pagination.per_page},set:function(n){return this.resetCheckedPeople(),this.$store.dispatch("invitables/setPerPage",n)}},subEventSelectFilter:{get:function(){var n=this;return _.debounce(function(){n.$store.state.invitables.data.filter.selects.sub_event},650)},set:function(n){return this.resetCheckedPeople(),this.$store.dispatch("invitables/mutateFilterSelect",{field:"sub_event",value:n})}}}),methods:{invitablesGotoPage:function(n){this.gotoPage(n,"invitables",this.invitables.data.links.pagination)},resetCheckedPeople:function(){for(var n in this.checkedPeople)this.checkedPeople.hasOwnProperty(n)&&(this.checkedPeople[n].checked=!1)},isChecked:function(n){return!!this.checkedPeople.hasOwnProperty(n.id)&&this.checkedPeople[n.id].checked},toggleCheck:function(n){this.checkedPeople.hasOwnProperty(n.id)||(this.checkedPeople[n.id]={id:n.id,checked:!1}),this.checkedPeople[n.id].checked=!this.checkedPeople[n.id].checked},invite:function(){var n={eventId:this.events.selected.id,subEventId:this.subEvents.selected.id,invitees:_.filter(this.checkedPeople,function(n){return n.checked})};this.resetCheckedPeople(),this.$store.dispatch("invitables/invite",n)},moveInvitations:function(){var n={eventId:this.events.selected.id,newSubEventId:this.subEvents.selected.id,currentSubEventId:this.selectedSubEvent,invitees:_.filter(this.checkedPeople,function(n){return n.checked})};this.resetCheckedPeople(),this.$store.dispatch("invitables/moveInvitations",n)},except:function(n){function e(e,t){return n.apply(this,arguments)}return e.toString=function(){return n.toString()},e}(function(n,e){var t=clone(n);return t.rows=except(n.rows,e),t}),sortInvitations:function(n){return _.sortBy(n,"order")}},beforeDestroy:function(){this.$store.state.invitables.data.filter.text=null,this.$store.commit("invitables/clearFilterSelects")}},p=(t("LY3I"),t("KHd+")),b=Object(p.a)(d,function(){var n=this,e=n.$createElement,t=n._self._c||e;return t("div",[t("div",{staticClass:"py-2 text-center"},[t("h1",[n._v("Convidar pessoas para o sub-evento")]),n._v(" "),t("h2",[n._v(n._s(n.events.selected.name))]),n._v(" "),t("h2",[n._v(n._s(n.subEvents.selected.name))]),n._v(" "),t("app-sector-badge",{key:"invitation.id",class:n.key>0?"mt-2 p-4":"",attrs:{sector:n.subEvents.selected.sector,"font-size":"2em",uppercase:"true",complement:n.subEvents.selected.place,complementFontSize:"1.2em",padding:"3"}})],1),n._v(" "),t("div",{staticClass:"row justify-content-center"},[t("div",{staticClass:"col-12"},[n.invitables.data.links?t("app-table-panel",{attrs:{title:"Pessoas","per-page":n.invitablesPerPage,"filter-text":n.invitablesFilterText},on:{"set-per-page":function(e){n.invitablesPerPage=e},"input-filter-text":function(e){n.invitablesFilterText=e.target.value}}},[t("template",{slot:"checkboxes"},[t("app-input",{attrs:{name:"not_invited",label:"Somente não convidados",type:"checkbox",required:!0,form:n.form,inline:"true"},model:{value:n.notInvited,callback:function(e){n.notInvited=e},expression:"notInvited"}})],1),n._v(" "),t("template",{slot:"selects"},[t("app-institution-filter-for-invitation",{attrs:{name:"institution_id",label:"Instituição",required:!0,form:n.form,options:n.environment.tables.institutions}}),n._v(" "),t("app-role-filter-for-invitation",{attrs:{name:"role_id",label:"Função",required:!0,form:n.form,options:n.environment.tables.roles}}),n._v(" "),t("app-select",{attrs:{name:"sub_event_id",label:"Filtrar convidados de outro sub-evento",required:!0,form:n.form,options:n.except(this.environment.tables.sub_events,this.subEvents.form.fields.id)},model:{value:n.subEventSelectFilter,callback:function(e){n.subEventSelectFilter=e},expression:"subEventSelectFilter"}})],1),n._v(" "),t("template",{slot:"buttons"},[t("div",{staticClass:"btn btn-primary btn-sm pull-right",on:{click:function(e){n.invite()}}},[n._v("\n                        "+n._s(n.recordButtonText)+"\n                    ")]),n._v(" "),n.selectedSubEvent?t("div",{staticClass:"btn btn-danger btn-sm pull-right",on:{click:function(e){n.moveInvitations()}}},[n._v("\n                        mover convidados\n                    ")]):n._e()]),n._v(" "),t("app-table",{attrs:{pagination:n.invitables.data.links.pagination,columns:["#","Convidar","Tratamento","Nome","Instituição","Cargo",{title:"Convites",trClass:"text-center"},""]},on:{"goto-page":function(e){n.invitablesGotoPage(e)}}},n._l(n.invitables.data.rows,function(e){return t("tr",{class:{"cursor-pointer":!0,"bg-primary-lighter text-white":n.isCurrent(e,n.invitables.selected)}},[t("td",{staticClass:"align-middle"},[n._v(n._s(e.id))]),n._v(" "),t("td",{staticClass:"align-middle"},[e.is_invited_to_sub_event?n._e():t("input",{attrs:{type:"checkbox"},domProps:{checked:n.isChecked(e)},on:{input:function(t){n.toggleCheck(e)}}})]),n._v(" "),t("td",{staticClass:"align-middle"},[n._v(n._s(e.title))]),n._v(" "),t("td",{staticClass:"align-middle"},[n._v("\n                            "+n._s(e.person.name)+"\n                        ")]),n._v(" "),t("td",{staticClass:"align-middle"},[n._v("\n                            "+n._s(e.institution.name)+"\n                        ")]),n._v(" "),t("td",{staticClass:"align-middle"},[n._v("\n                            "+n._s(e.role.name)+"\n                        ")]),n._v(" "),t("td",{staticClass:"align-middle text-center"},n._l(n.sortInvitations(e.invitations),function(i,s,r){return t("span",[t("app-sector-badge",{key:"invitation.id",class:s>0?"mt-2":"",attrs:{sector:i.sub_event.sector,uppercase:"true",complement:i.sub_event.place}}),n._v(" "),n.sortInvitations(e.invitations).length>1&&s<n.sortInvitations(e.invitations).length-1?t("br"):n._e()],1)}),0)])}),0)],2):n._e()],1)])])},[],!1,null,null,null);b.options.__file="InviteForm.vue";e.default=b.exports}}]);