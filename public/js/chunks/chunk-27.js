(window.webpackJsonp=window.webpackJsonp||[]).push([[27],{"N4/z":function(e,t,n){"use strict";var i=n("L2JU");function s(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{},i=Object.keys(n);"function"==typeof Object.getOwnPropertySymbols&&(i=i.concat(Object.getOwnPropertySymbols(n).filter(function(e){return Object.getOwnPropertyDescriptor(n,e).enumerable}))),i.forEach(function(t){o(e,t,n[t])})}return e}function o(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}t.a={methods:s({},Object(i.mapActions)("subEvents",["clearForm"])),computed:s({},Object(i.mapState)({addresses:function(e){return e.addresses},events:function(e){return e.events},subEvents:function(e){return e.subEvents}}))}},hsPB:function(e,t,n){"use strict";n.r(t);var i=n("jx1L"),s=n("N4/z"),o=n("L2JU");function r(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{},i=Object.keys(n);"function"==typeof Object.getOwnPropertySymbols&&(i=i.concat(Object.getOwnPropertySymbols(n).filter(function(e){return Object.getOwnPropertyDescriptor(n,e).enumerable}))),i.forEach(function(t){a(e,t,n[t])})}return e}function a(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var c={methods:r({},Object(o.mapActions)("invitables",["clearForm"])),computed:r({},Object(o.mapState)({invitables:function(e){return e.invitables}}))},l=n("CtO9");function u(e,t,n){return t in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}var d={name:"invitables",uri:"events/{events.selected.id}/sub-events/{subEvents.selected.id}/invitations/invitables"},v={props:["mode"],mixins:[i.a,c,s.a,l.a],data:function(){return this.$store.dispatch("environment/loadSubEvents"),this.$store.dispatch("environment/loadRoles"),this.$store.dispatch("environment/loadCategories"),this.$store.dispatch("environment/loadTopics"),this.$store.dispatch("environment/loadInstitutions"),{service:d,checkedPeople:{}}},computed:function(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{},i=Object.keys(n);"function"==typeof Object.getOwnPropertySymbols&&(i=i.concat(Object.getOwnPropertySymbols(n).filter(function(e){return Object.getOwnPropertyDescriptor(n,e).enumerable}))),i.forEach(function(t){u(e,t,n[t])})}return e}({},Object(o.mapState)({events:function(e){return e.events},subEvents:function(e){return e.subEvents}}),{notInvited:{get:function(){return this.$store.state.invitations.data.filter.checkboxes.not_invited},set:function(e){e?this.$store.commit("invitables/mutateFilterCheckbox",{field:"not_invited",value:this.subEvents.selected.id}):this.$store.commit("invitables/mutateFilterCheckbox",{field:"not_invited",value:null}),this.$store.dispatch("invitables/load")}},selectedSubEvent:{get:function(){return this.$store.state.invitables.data.filter.selects.sub_event}},recordButtonText:{get:function(){return this.selectedSubEvent?"copiar convidados":"convidar"}},invitablesFilterText:{get:function(){return this.$store.state.invitables.data.filter.text},set:function(e){return this.resetCheckedPeople(),this.$store.dispatch(this.service.name+"/mutateSetQueryFilterText",e)}},invitablesPerPage:{get:function(){return this.$store.state.invitables.data.links.pagination.per_page},set:function(e){return this.resetCheckedPeople(),this.$store.dispatch("invitables/setPerPage",e)}},subEventSelectFilter:{get:function(){var e=this;return _.debounce(function(){e.$store.state.invitables.data.filter.selects.sub_event},650)},set:function(e){return this.resetCheckedPeople(),this.$store.dispatch("invitables/mutateFilterSelect",{field:"sub_event",value:e})}}}),methods:{invitablesGotoPage:function(e){this.gotoPage(e,"invitables",this.invitables.data.links.pagination)},resetCheckedPeople:function(){for(var e in this.checkedPeople)this.checkedPeople.hasOwnProperty(e)&&(this.checkedPeople[e].checked=!1,document.getElementById("invitable-checkbox"+e)&&(document.getElementById("invitable-checkbox"+e).checked=!1));document.getElementById("check-all").checked=!1},isChecked:function(e){return!!(this.checkedPeople.hasOwnProperty(e)&&this.checkedPeople[e]&&this.checkedPeople[e].checked)},checkInvitable:function(e){this.isChecked(e.id)||this.toggleCheck(e)},checkAllPeople:function(){for(var e in this.invitables.data.rows)if(this.invitables.data.rows.hasOwnProperty(e)){var t=this.invitables.data.rows[e];t.is_invited_to_sub_event||this.checkInvitable(t)}},toggleCheckAllPeople:function(e){e.target.checked?this.checkAllPeople():this.resetCheckedPeople()},toggleCheck:function(e){this.checkedPeople.hasOwnProperty(e.id)||(this.checkedPeople[e.id]={id:e.id,checked:!1}),document.getElementById("invitable-checkbox"+e.id).checked=this.checkedPeople[e.id].checked=!this.checkedPeople[e.id].checked},invite:function(){var e={eventId:this.events.selected.id,subEventId:this.subEvents.selected.id,invitees:_.filter(this.checkedPeople,function(e){return e.checked})};this.resetCheckedPeople(),this.$store.dispatch("invitables/invite",e)},moveInvitations:function(){var e={eventId:this.events.selected.id,newSubEventId:this.subEvents.selected.id,currentSubEventId:this.selectedSubEvent,invitees:_.filter(this.checkedPeople,function(e){return e.checked})};this.resetCheckedPeople(),this.$store.dispatch("invitables/moveInvitations",e)},except:function(e){function t(t,n){return e.apply(this,arguments)}return t.toString=function(){return e.toString()},t}(function(e,t){var n=clone(e);return n.rows=except(e.rows,t),n}),sortInvitations:function(e){return _.sortBy(e,"order")}},beforeDestroy:function(){this.$store.state.invitables.data.filter.text=null,this.$store.commit("invitables/clearFilterSelects")}},b=n("KHd+"),p=Object(b.a)(v,function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",[n("div",{staticClass:"py-2 text-center"},[n("h1",[e._v("Convidar pessoas para o sub-evento")]),e._v(" "),n("h2",[e._v(e._s(e.events.selected.name))]),e._v(" "),n("h2",[e._v(e._s(e.subEvents.selected.name))]),e._v(" "),n("app-sector-badge",{key:"invitation.id",staticClass:"mt-2 p-4",attrs:{sector:e.subEvents.selected.sector,"font-size":"2em",uppercase:"true",complement:e.subEvents.selected.place,complementFontSize:"1.2em",padding:"3"}})],1),e._v(" "),n("div",{staticClass:"row justify-content-center"},[n("div",{staticClass:"col-12"},[e.invitables.data.links?n("app-table-panel",{attrs:{title:"Pessoas","per-page":e.invitablesPerPage,"filter-text":e.invitablesFilterText},on:{"set-per-page":function(t){e.invitablesPerPage=t},"input-filter-text":function(t){e.invitablesFilterText=t.target.value}}},[n("template",{slot:"checkboxes"},[n("app-input",{attrs:{name:"not_invited",label:"Somente não convidados",type:"checkbox",required:!0,form:e.form,inline:"true"},model:{value:e.notInvited,callback:function(t){e.notInvited=t},expression:"notInvited"}})],1),e._v(" "),n("template",{slot:"selects"},[n("app-institution-filter-for-invitation",{attrs:{name:"institution_id",label:"Instituição",required:!0,form:e.form,options:e.environment.tables.institutions}}),e._v(" "),n("app-role-filter-for-invitation",{attrs:{name:"role_id",label:"Função",required:!0,form:e.form,options:e.environment.tables.roles}}),e._v(" "),n("app-topic-filter-for-invitation",{attrs:{name:"topic_id",label:"Interesses",required:!0,form:e.form,options:e.environment.tables.topics}}),e._v(" "),n("app-category-filter-for-invitation",{attrs:{name:"category_id",label:"Categoria",required:!0,form:e.form,options:e.environment.tables.categories}}),e._v(" "),n("app-select",{attrs:{name:"sub_event_id",label:"Filtrar convidados de outro sub-evento",required:!0,form:e.form,options:e.except(this.environment.tables.sub_events,this.subEvents.form.fields.id)},model:{value:e.subEventSelectFilter,callback:function(t){e.subEventSelectFilter=t},expression:"subEventSelectFilter"}})],1),e._v(" "),n("template",{slot:"buttons"},[n("div",{staticClass:"btn btn-primary btn-sm pull-right",attrs:{dusk:"dusk-invite-button",disabled:e.cannot("subevents:invite")},on:{click:function(t){return e.invite()}}},[e._v("\n                        "+e._s(e.recordButtonText)+"\n                    ")]),e._v(" "),e.selectedSubEvent?n("div",{staticClass:"btn btn-danger btn-sm pull-right",attrs:{disabled:e.cannot("subevents:invite")},on:{click:function(t){return e.moveInvitations()}}},[e._v("\n                        mover convidados\n                    ")]):e._e()]),e._v(" "),n("app-table",{attrs:{pagination:e.invitables.data.links.pagination,columns:["#",{type:"checkbox",trClass:"",id:"check-all"},"Tratamento","Nome","Instituição","Cargo",{type:"label",title:"Convites",trClass:"text-center"},""]},on:{"goto-page":function(t){return e.invitablesGotoPage(t)},"input-checkbox-check-all":function(t){return e.toggleCheckAllPeople(t)}}},e._l(e.invitables.data.rows,function(t){return n("tr",{class:{"cursor-pointer":!0,"bg-primary-lighter text-white":e.isCurrent(t,e.invitables.selected)}},[n("td",{staticClass:"align-middle"},[e._v(e._s(t.id))]),e._v(" "),n("td",{staticClass:"align-middle"},[t.is_invited_to_sub_event?e._e():n("input",{attrs:{id:"invitable-checkbox"+t.id,type:"checkbox",dusk:"dusk-invite"},on:{input:function(n){return e.toggleCheck(t)}}})]),e._v(" "),n("td",{staticClass:"align-middle"},[e._v(e._s(t.title))]),e._v(" "),n("td",{staticClass:"align-middle"},[e._v("\n                            "+e._s(t.person.name)+"\n                        ")]),e._v(" "),n("td",{staticClass:"align-middle"},[e._v("\n                            "+e._s(t.institution.name)+"\n                        ")]),e._v(" "),n("td",{staticClass:"align-middle"},[e._v("\n                            "+e._s(t.role.name)+"\n                        ")]),e._v(" "),n("td",{staticClass:"align-middle text-center"},e._l(e.sortInvitations(t.invitations),function(i,s,o){return n("span",[n("app-sector-badge",{key:"invitation.id",class:s>0?"mt-2":"",attrs:{sector:i.sub_event.sector,uppercase:"true",complement:i.sub_event.place}}),e._v(" "),e.sortInvitations(t.invitations).length>1&&s<e.sortInvitations(t.invitations).length-1?n("br"):e._e()],1)}),0)])}),0)],2):e._e()],1)])])},[],!1,null,null,null);t.default=p.exports}}]);