(function(a,q,r){function c(a){a=a||location.href;return"#"+a.replace(/^[^#]*#?(.*)$/,"$1")}var k=document,d,f=a.event.special,t=k.documentMode,m="onhashchange"in q&&(t===r||7<t);a.fn.hashchange=function(a){return a?this.bind("hashchange",a):this.trigger("hashchange")};a.fn.hashchange.delay=50;f.hashchange=a.extend(f.hashchange,{setup:function(){if(m)return!1;a(d.start)},teardown:function(){if(m)return!1;a(d.stop)}});d=function(){function d(){var e=c(),b=f(l);e!==l?(n(l=e,b),a(q).trigger("hashchange")):
b!==l&&(location.href=location.href.replace(/#.*/,"")+b);g=setTimeout(d,a.fn.hashchange.delay)}var h={},g,l=c(),p=function(a){return a},n=p,f=p;h.start=function(){g||d()};h.stop=function(){g&&clearTimeout(g);g=r};a.browser.msie&&!m&&function(){var e,b;h.start=function(){e||(b=(b=a.fn.hashchange.src)&&b+c(),e=a('<iframe tabindex="-1" title="empty"/>').hide().one("load",function(){b||n(c());d()}).attr("src",b||"javascript:0").insertAfter("body")[0].contentWindow,k.onpropertychange=function(){try{"title"===
event.propertyName&&(e.document.title=k.title)}catch(a){}})};h.stop=p;f=function(){return c(e.location.href)};n=function(b,d){var c=e.document,f=a.fn.hashchange.domain;b!==d&&(c.title=k.title,c.open(),f&&c.write('<script>document.domain="'+f+'"\x3c/script>'),c.close(),e.location.hash=b)}}();return h}()})(jQuery,this);