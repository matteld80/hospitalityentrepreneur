// Table sorter script from scriptiny.com
// under Creative Commons License http://creativecommons.org/licenses/by/3.0/us/
function T$(e){return document.getElementById(e)}function T$$(e,t){return t.getElementsByTagName(e)}var TINY={};TINY.table=function(){function e(e,t,n){this.n=e;this.id=t;this.p=n;if(this.p.init){this.init()}}function t(e,t){return Math.round(e*Math.pow(10,t))/Math.pow(10,t)}function r(e,t){var n,r;e=n=e.v.toLowerCase();t=r=t.v.toLowerCase();var i=parseFloat(e.replace(/(\$|\,)/g,"")),s=parseFloat(t.replace(/(\$|\,)/g,""));if(!isNaN(i)&&!isNaN(s)){n=i,r=s}i=Date.parse(e);s=Date.parse(t);if(!isNaN(i)&&!isNaN(s)){n=i;r=s}return n>r?1:n<r?-1:0}e.prototype.init=function(){this.set();var e=this.t,t=d=0;e.h=T$$("tr",e)[0];e.l=e.r.length;e.w=e.r[0].cells.length;e.a=[];e.c=[];this.p.is=this.p.size;if(this.p.colddid){d=T$(this.p.colddid);var n=document.createElement("option");n.value=-1;n.innerHTML="All Columns";d.appendChild(n)}for(t;t<e.w;t++){var r=e.h.cells[t];e.c[t]={};if(r.className!="nosort"){r.className=this.p.headclass;r.onclick=new Function(this.n+".sort("+t+")");r.onmousedown=function(){return false}}if(this.p.columns){var i=this.p.columns.length,s=0;for(s;s<i;s++){if(this.p.columns[s].index==t){var o=this.p.columns[s];e.c[t].format=o.format==null?1:o.format;e.c[t].decimals=o.decimals==null?2:o.decimals}}}if(d){var n=document.createElement("option");n.value=t;var u=T$$("strong",r);if(u.length>0){n.innerHTML=u[0].innerHTML;d.appendChild(n)}}}this.reset()};e.prototype.reset=function(){var e=this.t;e.t=e.l;for(var t=0;t<e.l;t++){e.a[t]={};e.a[t].s=1}if(this.p.sortcolumn!=undefined){this.sort(this.p.sortcolumn,1,this.p.is)}else{if(this.p.paginate){this.size()}this.alt();this.sethover()}this.calc()};e.prototype.sort=function(e,t,n){var i=this.t;i.y=e;var e=i.h.cells[i.y],s=0,o=document.createElement("tbody");for(s;s<i.l;s++){i.a[s].o=s;var u=i.r[s].cells[i.y];i.r[s].style.display="";while(u.hasChildNodes()){u=u.firstChild}i.a[s].v=u.nodeValue?u.nodeValue:""}for(s=0;s<i.w;s++){var a=i.h.cells[s];if(a.className!="nosort"){a.className=this.p.headclass}}if(i.p==i.y&&!t){i.a.reverse();e.className=i.d?this.p.ascclass:this.p.descclass;i.d=i.d?0:1}else{i.p=i.y;t&&this.p.sortdir==-1?i.a.sort(r).reverse():i.a.sort(r);i.d=0;e.className=this.p.ascclass}for(s=0;s<i.l;s++){var f=i.r[i.a[s].o].cloneNode(true);o.appendChild(f)}i.replaceChild(o,i.b);this.set();this.alt();if(this.p.paginate){this.size(n)}this.sethover()};e.prototype.sethover=function(){if(this.p.hoverid){for(var e=0;e<this.t.l;e++){var t=this.t.r[e];t.setAttribute("onmouseover",this.n+".hover("+e+",1)");t.setAttribute("onmouseout",this.n+".hover("+e+",0)")}}};e.prototype.calc=function(){if(this.p.sum||this.p.avg){var e=this.t,n=x=0,r,i;if(!T$$("tfoot",e)[0]){r=document.createElement("tfoot");e.appendChild(r)}else{r=T$$("tfoot",e)[0];while(r.hasChildNodes()){r.removeChild(r.firstChild)}}if(this.p.sum){i=this.newrow(r);for(n;n<e.w;n++){var s=i.cells[n];if(this.p.sum.exists(n)){var o=0,u=e.c[n].format||"";for(x=0;x<this.t.l;x++){if(e.a[x].s){o+=parseFloat(e.r[x].cells[n].innerHTML.replace(/(\$|\,)/g,""))}}o=t(o,e.c[n].decimals?e.c[n].decimals:2);o=isNaN(o)?"n/a":u=="$"?o=o.currency(e.c[n].decimals):o+u;i.cells[n].innerHTML=o;i.cells[0].innerHTML="TOTAL"}else{i.cells[n].innerHTML="&nbsp;"}}}if(this.p.avg){i=this.newrow(r);for(n=0;n<e.w;n++){var s=i.cells[n];if(this.p.avg.exists(n)){var o=c=0,u=e.c[n].format||"";for(x=0;x<this.t.l;x++){if(e.a[x].s){o+=parseFloat(e.r[x].cells[n].innerHTML.replace(/(\$|\,)/g,""));c++}}o=t(o/c,e.c[n].decimals?e.c[n].decimals:2);o=isNaN(o)?"n/a":u=="$"?o=o.currency(e.c[n].decimals):o+u;s.innerHTML=o}else{s.innerHTML="&nbsp;"}}}}};e.prototype.newrow=function(e){var t=document.createElement("tr"),n=0;e.appendChild(t);for(n;n<this.t.w;n++){t.appendChild(document.createElement("td"))}return t};e.prototype.alt=function(){var e=this.t,t=x=0;for(t;t<e.l;t++){var n=e.r[t];if(e.a[t].s){n.className=x%2==0?this.p.evenclass:this.p.oddclass;var r=T$$("td",n);for(var i=0;i<e.w;i++){r[i].className=e.y==i?x%2==0?this.p.evenselclass:this.p.oddselclass:""}x++}if(!e.a[t].s){n.style.display="none"}}};e.prototype.page=function(e){var t=this.t,n=x=0,r=e+parseInt(this.p.size);if(this.p.totalrecid){T$(this.p.totalrecid).innerHTML=t.t}if(this.p.currentid){T$(this.p.currentid).innerHTML=this.g}if(this.p.pageddid){T$(this.p.pageddid).value=this.g}if(this.p.startingrecid){var i=(this.g-1)*this.p.size+1,s=i+(this.p.size-1);s=s<t.l?s:t.t;s=s<t.t?s:t.t;T$(this.p.startingrecid).innerHTML=t.t==0?0:i;T$(this.p.endingrecid).innerHTML=s}for(n;n<t.l;n++){var o=t.r[n];if(t.a[n].s){o.style.display=x>=e&&x<r?"":"none";x++}else{o.style.display="none"}}};e.prototype.move=function(e,t){this.goto(e==1?t?this.d:this.g+1:t?1:this.g-1)};e.prototype.goto=function(e){if(e<=this.d&&e>0){this.g=e;this.page((e-1)*this.p.size)}};e.prototype.size=function(e){var t=this.t;if(e){this.p.size=e}this.g=1;this.d=Math.ceil(this.t.t/this.p.size);if(this.p.navid){T$(this.p.navid).style.display=this.d<2?"none":"block"}this.page(0);if(this.p.totalid){T$(this.p.totalid).innerHTML=t.t==0?1:this.d}if(this.p.pageddid){var n=T$(this.p.pageddid),r=this.d+1;n.setAttribute("onchange",this.n+".goto(this.value)");while(n.hasChildNodes()){n.removeChild(n.firstChild)}for(var i=1;i<=this.d;i++){var s=document.createElement("option");s.value=i;s.innerHTML=i;n.appendChild(s)}}};e.prototype.showall=function(){this.size(this.t.t)};e.prototype.search=function(e){var t=x=n=0,r=-1,i=T$(e).value.toLowerCase();if(this.p.colddid){r=T$(this.p.colddid).value}var s=r==-1?0:r,o=r==-1?this.t.w:parseInt(s)+1;for(t;t<this.t.l;t++){var u=this.t.r[t],a;if(i==""){a=1}else{for(x=s;x<o;x++){var f=u.cells[x].innerHTML.toLowerCase();if(f.indexOf(i)==-1){a=0}else{a=1;break}}}if(a){n++}this.t.a[t].s=a}this.t.t=n;if(this.p.paginate){this.size()}this.calc();this.alt()};e.prototype.hover=function(e,t){this.t.r[e].id=t?this.p.hoverid:""};e.prototype.set=function(){var e=T$(this.id);e.b=T$$("tbody",e)[0];e.r=e.b.rows;this.t=e};Array.prototype.exists=function(e){for(var t=0;t<this.length;t++){if(this[t]==e){return 1}}return 0};Number.prototype.currency=function(e){var t=this,n=t.toFixed(e).split(".");n[0]=n[0].split("").reverse().join("").replace(/(\d{3})(?=\d)/g,"$1,").split("").reverse().join("");return"$"+n.join(".")};return{sorter:e}}()