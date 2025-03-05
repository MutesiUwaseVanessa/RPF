import{Command as e,Plugin as t,icons as i}from"@ckeditor/ckeditor5-core";import{ButtonView as o,createDropdown as n,SplitButtonView as l,addToolbarToDropdown as r,MenuBarMenuView as h,MenuBarMenuListView as c,MenuBarMenuListItemView as a,MenuBarMenuListItemButtonView as s,ListSeparatorView as g,ToolbarSeparatorView as d}from"@ckeditor/ckeditor5-ui";
/**
 * @license Copyright (c) 2003-2024, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-licensing-options
 */class u extends e{refresh(){const e=this.editor.model,t=e.document;this.value=t.selection.getAttribute("highlight"),this.isEnabled=e.schema.checkAttributeInSelection(t.selection,"highlight")}execute(e={}){const t=this.editor.model,i=t.document.selection,o=e.value;t.change((e=>{if(i.isCollapsed){const t=i.getFirstPosition();if(i.hasAttribute("highlight")){const i=e=>e.item.hasAttribute("highlight")&&e.item.getAttribute("highlight")===this.value,n=t.getLastMatchingPosition(i,{direction:"backward"}),l=t.getLastMatchingPosition(i),r=e.createRange(n,l);o&&this.value!==o?(t.isEqual(l)||e.setAttribute("highlight",o,r),e.setSelectionAttribute("highlight",o)):(t.isEqual(l)||e.removeAttribute("highlight",r),e.removeSelectionAttribute("highlight"))}else o&&e.setSelectionAttribute("highlight",o)}else{const n=t.schema.getValidRanges(i.getRanges(),"highlight");for(const t of n)o?e.setAttribute("highlight",o,t):e.removeAttribute("highlight",t)}}))}}
/**
 * @license Copyright (c) 2003-2024, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-licensing-options
 */class m extends t{static get pluginName(){return"HighlightEditing"}static get isOfficialPlugin(){return!0}constructor(e){super(e),e.config.define("highlight",{options:[{model:"yellowMarker",class:"marker-yellow",title:"Yellow marker",color:"var(--ck-highlight-marker-yellow)",type:"marker"},{model:"greenMarker",class:"marker-green",title:"Green marker",color:"var(--ck-highlight-marker-green)",type:"marker"},{model:"pinkMarker",class:"marker-pink",title:"Pink marker",color:"var(--ck-highlight-marker-pink)",type:"marker"},{model:"blueMarker",class:"marker-blue",title:"Blue marker",color:"var(--ck-highlight-marker-blue)",type:"marker"},{model:"redPen",class:"pen-red",title:"Red pen",color:"var(--ck-highlight-pen-red)",type:"pen"},{model:"greenPen",class:"pen-green",title:"Green pen",color:"var(--ck-highlight-pen-green)",type:"pen"}]})}init(){const e=this.editor;e.model.schema.extend("$text",{allowAttributes:"highlight"});const t=e.config.get("highlight.options");e.conversion.attributeToElement(function(e){const t={model:{key:"highlight",values:[]},view:{}};for(const i of e)t.model.values.push(i.model),t.view[i.model]={name:"mark",classes:i.class};return t}(t)),e.commands.add("highlight",new u(e))}}!function(e,{insertAt:t}={}){if("undefined"==typeof document)return;const i=document.head||document.getElementsByTagName("head")[0],o=document.createElement("style");o.type="text/css",window.litNonce&&o.setAttribute("nonce",window.litNonce),"top"===t&&i.firstChild?i.insertBefore(o,i.firstChild):i.appendChild(o),o.styleSheet?o.styleSheet.cssText=e:o.appendChild(document.createTextNode(e))}(":root{--ck-highlight-marker-yellow:#fdfd77;--ck-highlight-marker-green:#62f962;--ck-highlight-marker-pink:#fc7899;--ck-highlight-marker-blue:#72ccfd;--ck-highlight-pen-red:#e71313;--ck-highlight-pen-green:#128a00}.ck-content .marker-yellow{background-color:var(--ck-highlight-marker-yellow)}.ck-content .marker-green{background-color:var(--ck-highlight-marker-green)}.ck-content .marker-pink{background-color:var(--ck-highlight-marker-pink)}.ck-content .marker-blue{background-color:var(--ck-highlight-marker-blue)}.ck-content .pen-red{background-color:transparent;color:var(--ck-highlight-pen-red)}.ck-content .pen-green{background-color:transparent;color:var(--ck-highlight-pen-green)}");
/**
 * @license Copyright (c) 2003-2024, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-licensing-options
 */
class k extends t{get localizedOptionTitles(){const e=this.editor.t;return{"Yellow marker":e("Yellow marker"),"Green marker":e("Green marker"),"Pink marker":e("Pink marker"),"Blue marker":e("Blue marker"),"Red pen":e("Red pen"),"Green pen":e("Green pen")}}static get pluginName(){return"HighlightUI"}static get isOfficialPlugin(){return!0}init(){const e=this.editor.config.get("highlight.options");for(const t of e)this._addHighlighterButton(t);this._addRemoveHighlightButton(),this._addDropdown(e),this._addMenuBarButton(e)}_addRemoveHighlightButton(){const e=this.editor.t,t=this.editor.commands.get("highlight");this._addButton("removeHighlight",e("Remove highlight"),i.eraser,null,(e=>{e.bind("isEnabled").to(t,"isEnabled")}))}_addHighlighterButton(e){const t=this.editor.commands.get("highlight");this._addButton("highlight:"+e.model,e.title,p(e.type),e.model,(function(i){i.bind("isEnabled").to(t,"isEnabled"),i.bind("isOn").to(t,"value",(t=>t===e.model)),i.iconView.fillColor=e.color,i.isToggleable=!0}))}_addButton(e,t,i,n,l){const r=this.editor;r.ui.componentFactory.add(e,(e=>{const h=new o(e),c=this.localizedOptionTitles[t]?this.localizedOptionTitles[t]:t;return h.set({label:c,icon:i,tooltip:!0}),h.on("execute",(()=>{r.execute("highlight",{value:n}),r.editing.view.focus()})),l(h),h}))}_addDropdown(e){const t=this.editor,i=t.t,o=t.ui.componentFactory,h=e[0],c=e.reduce(((e,t)=>(e[t.model]=t,e)),{});o.add("highlight",(a=>{const s=t.commands.get("highlight"),g=n(a,l),u=g.buttonView;u.set({label:i("Highlight"),tooltip:!0,lastExecuted:h.model,commandValue:h.model,isToggleable:!0}),u.bind("icon").to(s,"value",(e=>p(m(e,"type")))),u.bind("color").to(s,"value",(e=>m(e,"color"))),u.bind("commandValue").to(s,"value",(e=>m(e,"model"))),u.bind("isOn").to(s,"value",(e=>!!e)),u.delegate("execute").to(g);function m(e,t){const i=e&&e!==u.lastExecuted?e:u.lastExecuted;return c[i][t]}return g.bind("isEnabled").to(s,"isEnabled"),r(g,(()=>{const t=e.map((e=>{const t=o.create("highlight:"+e.model);return this.listenTo(t,"execute",(()=>{g.buttonView.set({lastExecuted:e.model})})),t}));return t.push(new d),t.push(o.create("removeHighlight")),t}),{enableActiveItemFocusOnDropdownOpen:!0,ariaLabel:i("Text highlight toolbar")}),function(e){const t=e.buttonView.actionView;t.iconView.bind("fillColor").to(e.buttonView,"color")}(g),u.on("execute",(()=>{t.execute("highlight",{value:u.commandValue})})),this.listenTo(g,"execute",(()=>{t.editing.view.focus()})),g}))}_addMenuBarButton(e){const t=this.editor,o=t.t,n=t.commands.get("highlight");t.ui.componentFactory.add("menuBar:highlight",(l=>{const r=new h(l);r.buttonView.set({label:o("Highlight"),icon:p("marker")}),r.bind("isEnabled").to(n),r.buttonView.iconView.fillColor="transparent";const d=new c(l);for(const i of e){const e=new a(l,r),o=new s(l);o.set({label:i.title,icon:p(i.type),role:"menuitemradio",isToggleable:!0}),o.iconView.fillColor=i.color,o.delegate("execute").to(r),o.bind("isOn").to(n,"value",(e=>e===i.model)),o.on("execute",(()=>{t.execute("highlight",{value:i.model}),t.editing.view.focus()})),e.children.add(o),d.items.add(e)}d.items.add(new g(l));const u=new a(l,r),m=new s(l);return m.set({label:o("Remove highlight"),icon:i.eraser}),m.delegate("execute").to(r),m.on("execute",(()=>{t.execute("highlight",{value:null}),t.editing.view.focus()})),u.children.add(m),d.items.add(u),r.panelView.children.add(d),r}))}}function p(e){return"marker"===e?'<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path class="ck-icon__fill" d="M10.798 1.59 3.002 12.875l1.895 1.852 2.521 1.402 6.997-12.194z"/><path d="m2.556 16.727.234-.348c-.297-.151-.462-.293-.498-.426-.036-.137.002-.416.115-.837.094-.25.15-.449.169-.595a4.495 4.495 0 0 0 0-.725c-.209-.621-.303-1.041-.284-1.26.02-.218.178-.506.475-.862l6.77-9.414c.539-.91 1.605-.85 3.199.18 1.594 1.032 2.188 1.928 1.784 2.686l-5.877 10.36c-.158.412-.333.673-.526.782-.193.108-.604.179-1.232.21-.362.131-.608.237-.738.318-.13.081-.305.238-.526.47-.293.265-.504.397-.632.397-.096 0-.27-.075-.524-.226l-.31.41-1.6-1.12zm-.279.415 1.575 1.103-.392.515H1.19l1.087-1.618zm8.1-13.656-4.953 6.9L8.75 12.57l4.247-7.574c.175-.25-.188-.647-1.092-1.192-.903-.546-1.412-.652-1.528-.32zM8.244 18.5 9.59 17h9.406v1.5H8.245z"/></svg>':'<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path class="ck-icon__fill" d="M10.126 2.268 2.002 13.874l1.895 1.852 2.521 1.402L14.47 5.481l-1.543-2.568-2.801-.645z"/><path d="m4.5 18.088-2.645-1.852-.04-2.95-.006-.005.006-.008v-.025l.011.008L8.73 2.97c.165-.233.356-.417.567-.557l-1.212.308L4.604 7.9l-.83-.558 3.694-5.495 2.708-.69 1.65 1.145.046.018.85-1.216 2.16 1.512-.856 1.222c.828.967 1.144 2.141.432 3.158L7.55 17.286l.006.005-3.055.797H4.5zm-.634.166-1.976.516-.026-1.918 2.002 1.402zM9.968 3.817l-.006-.004-6.123 9.184 3.277 2.294 6.108-9.162.005.003c.317-.452-.16-1.332-1.064-1.966-.891-.624-1.865-.776-2.197-.349zM8.245 18.5 9.59 17h9.406v1.5H8.245z"/></svg>'}
/**
 * @license Copyright (c) 2003-2024, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-licensing-options
 */class b extends t{static get requires(){return[m,k]}static get pluginName(){return"Highlight"}static get isOfficialPlugin(){return!0}}export{b as Highlight,m as HighlightEditing,k as HighlightUI};