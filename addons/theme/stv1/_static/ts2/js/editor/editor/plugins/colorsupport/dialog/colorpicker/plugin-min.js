KISSY.Editor.add("colorsupport/dialog/colorpicker",function(){function o(a){if(k.isArray(a))return a;var c=RegExp;if(/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i.test(a))return j([c.$1,c.$2,c.$3],function(e){return parseInt(e,16)});else if(/^#([0-9a-f])([0-9a-f])([0-9a-f])$/i.test(a))return j([c.$1,c.$2,c.$3],function(e){return parseInt(e+e,16)});else if(/^rgb\((.*),(.*),(.*)\)$/i.test(a))return j([c.$1,c.$2,c.$3],function(e){return e.indexOf("%")>0?parseFloat(e,10)*2.55:e|0})}function p(a){a="0"+
a;var c=a.length;return a.slice(c-2,c)}function q(a){a=o(a);return"#"+p(a[0].toString(16))+p(a[1].toString(16))+p(a[2].toString(16))}function s(){this._init()}var k=KISSY,l=k.Editor,j=l.Utils.map,t=k.DOM;t.addStyleSheet(".ke-color-advanced-picker-left {float:left;display:inline;margin-left:10px;}.ke-color-advanced-picker-right {float:right;width:50px;display:inline;margin:13px 10px 0 0;cursor:crosshair;}.ke-color-advanced-picker-right a {height:2px;line-height:0;font-size:0;display:block;}.ke-color-advanced-picker-left ul{float:left;}.ke-color-advanced-picker-left li,.ke-color-advanced-picker-left a{overflow:hidden;width:15px;height:16px;line-height:0;font-size:0;display:block;}.ke-color-advanced-picker-left a:hover{width:13px;height:13px;border:1px solid white;}.ke-color-advanced-indicator {margin-left:10px;*zoom:1;display:inline-block;*display:inline;width:68px;height:24px;vertical-align:middle;line-height:0;overflow:hidden;}",
"ke-color-advanced");var r=function(){function a(b,f,g){var h=[];b=c(b);f=c(f);var d=(f[0]-b[0])/g,i=(f[1]-b[1])/g,m=(f[2]-b[2])/g,n=0,u=b[0],v=b[1];for(b=b[2];n<g;n++){h[n]=[u,v,b];u+=d;v+=i;b+=m}h[n]=f;return j(h,function(w){return j(w,function(x){return Math.min(Math.max(0,Math.floor(x)),255)})})}function c(b){var f=o(b);if(f===undefined){if(!e){e=document.createElement("textarea");e.style.display="none";t.prepend(e,document.body)}try{e.style.color=b}catch(g){return[0,0,0]}if(document.defaultView)f=
o(document.defaultView.getComputedStyle(e,null).color);else{b=e.createTextRange().queryCommandValue("ForeColor");f=[b&255,(b&65280)>>>8,(b&16711680)>>>16]}}return f}var e;return function(b,f){var g=[],h=b.length;if(f===undefined)f=20;if(h==1)g=a(b[0],b[0],f);else if(h>1){var d=0;for(h=h-1;d<h;d++){var i=a(b[d],b[d+1],f[d]||f);d<h-1&&i.pop();g=g.concat(i)}}return g}}(),y="<div class='ke-color-advanced-picker'><div class='ks-clear'><div class='ke-color-advanced-picker-left'>"+("<ul>"+j(r(["red","orange",
"yellow","green","cyan","blue","purple"],5),function(a){return j(r(["white","rgb("+a.join(",")+")","black"],5),function(c){return"<li><a style='background-color:"+q(c)+"' href='#'></a></li>"}).join("")}).join("</ul><ul>")+"</ul>")+"</div><div class='ke-color-advanced-picker-right'></div></div><div style='padding:10px;'><label>\u989c\u8272\u503c\uff1a <input style='width:100px' class='ke-color-advanced-value'/></label><span class='ke-color-advanced-indicator'></span></div></div>",z=l.Utils.addRes,A=l.Utils.destroyRes;
k.augment(s,{_init:function(){var a=this;a.__res=[];a.win=new l.Dialog({mask:true,headerContent:"\u989c\u8272\u62fe\u53d6\u5668",bodyContent:y,footerContent:"<div style='padding:5px 20px 20px;'><a class='ke-button ke-color-advanced-ok'>\u786e\u5b9a</a>&nbsp;&nbsp;&nbsp;<a class='ke-button  ke-color-advanced-cancel'>\u53d6\u6d88</a></div>",autoRender:true,width:"550px"});var c=a.win,e=c.get("body"),b=c.get("footer"),f=e.one(".ke-color-advanced-indicator"),g=e.one(".ke-color-advanced-value"),h=e.one(".ke-color-advanced-picker-left");e.one(".ke-color-advanced-picker-right");
c=b.one(".ke-color-advanced-ok");b=b.one(".ke-color-advanced-cancel");c.on("click",function(d){var i=k.trim(g.val()),m=a.cmd;if(/^#([a-f0-9]{1,2}){3,3}$/i.test(i)){a.hide();setTimeout(function(){m.cfg._applyColor.call(m,g.val())},0);d&&d.halt()}else alert("\u8bf7\u8f93\u5165\u6b63\u786e\u7684\u989c\u8272\u4ee3\u7801")});g.on("change",function(){var d=k.trim(g.val());/^#([a-f0-9]{1,2}){3,3}$/i.test(d)?f.css("background-color",d):alert("\u8bf7\u8f93\u5165\u6b63\u786e\u7684\u989c\u8272\u4ee3\u7801")});b.on("click",function(d){a.hide();d&&d.halt()});e.on("click",function(d){d.halt();d=new k.Node(d.target);
if(d._4e_name()=="a"){var i=q(d.css("background-color"));h.contains(d)&&a._detailColor(i);g.val(i);f.css("background-color",i)}});z.call(a,c,g,b,e,a.win);a._detailColor("#FF9900");g.val("#FF9900");f.css("background-color","#FF9900")},_detailColor:function(a){this.win.get("body").one(".ke-color-advanced-picker-right").html(j(r(["#ffffff",a,"#000000"],40),function(c){return"<a style='background-color:"+q(c)+"'></a>"}).join(""))},show:function(a){this.cmd=a;this.win.show()},hide:function(){this.win.hide()},
destroy:function(){A.call(this)}});l.ColorSupport.ColorPicker=s},{attach:false});
