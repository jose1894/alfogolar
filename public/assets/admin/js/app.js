"use strict";$(".integer-validation").keypress((function(e){var t=e.charCode?e.charCode:e.keyCode;if(8!=t&&(t<2534||t>2543)&&(t<48||t>57))return!1})),$(".numeric-validation").keypress((function(e){var t=e.charCode?e.charCode:e.keyCode;if(8!=t&&"."!=e.key&&45!=t&&(t<2534||t>2543)&&(t<48||t>57))return!1})),$((function(){$("#sidebar__menuWrapper").slimScroll({height:"calc(100vh - 86.75px)"})})),$((function(){$(".dropdown-menu__body").slimScroll({height:"270px"})})),$((function(){$(".modal-dialog-scrollable .modal-body").slimScroll({height:"100%"})})),$((function(){$(".activity-list").slimScroll({height:"385px"})})),$((function(){$(".recent-ticket-list__body").slimScroll({height:"295px"})})),$(".modal").modal({backdrop:"static",keyboard:!1,show:!1}),$("#navbar-search__field").on("input",(function(){var e=$(this).val().toLowerCase(),t=$(".navbar_search_result");if($(t).html(""),0!=e.length){var n=$(".sidebar__menu-wrapper .nav-link").filter((function(t,n){return $(n).text().trim().toLowerCase().indexOf(e)>=0?n:null})).sort();0!=n.length?n.each((function(e,n){var a=$(n).attr("href")||$(n).data("default-url"),i=$(n).text().replace(/(\d+)/g,"").trim();$(t).append(`<li><a href="${a}">${i}</a></li>`)})):$(t).append('<li class="text-muted pl-5">No search result found.</li>')}}));let img=$(".bg_img");img.css("background-image",(function(){return"url("+$(this).data("background")+")"}));const navTgg=$(".navbar__expand");navTgg.on("click",(function(){$(this).toggleClass("active"),$(".sidebar").toggleClass("active"),$(".navbar-wrapper").toggleClass("active"),$(".body-wrapper").toggleClass("active")})),$((function(){$('[data-toggle="tooltip"]').tooltip()})),$(".nice-select").niceSelect(),$(".navbar-search__btn-open").on("click",(function(){$(".navbar-search").addClass("active")})),$(".navbar-search__close").on("click",(function(){$(".navbar-search").removeClass("active")})),$(".res-sidebar-open-btn").on("click",(function(){$(".sidebar").addClass("open")})),$(".res-sidebar-close-btn").on("click",(function(){$(".sidebar").removeClass("open")}));let elem=document.documentElement;function openFullscreen(){elem.requestFullscreen?elem.requestFullscreen():elem.mozRequestFullScreen?elem.mozRequestFullScreen():elem.webkitRequestFullscreen?elem.webkitRequestFullscreen():elem.msRequestFullscreen&&elem.msRequestFullscreen()}function closeFullscreen(){document.exitFullscreen?document.exitFullscreen():document.mozCancelFullScreen?document.mozCancelFullScreen():document.webkitExitFullscreen?document.webkitExitFullscreen():document.msExitFullscreen&&document.msExitFullscreen()}function proPicURL(e){if(e.files&&e.files[0]){var t=new FileReader;t.onload=function(t){var n=$(e).parents(".thumb").find(".profilePicPreview");$(n).css("background-image","url("+t.target.result+")"),$(n).addClass("has-image"),$(n).hide(),$(n).fadeIn(650)},t.readAsDataURL(e.files[0])}}$(".fullscreen-btn").on("click",(function(){$(this).toggleClass("active")})),$(".sidebar-dropdown > a").on("click",(function(){$(this).parent().find(".sidebar-submenu").length&&($(this).parent().find(".sidebar-submenu").first().is(":visible")?($(this).find(".side-menu__sub-icon").removeClass("transform rotate-180"),$(this).removeClass("side-menu--open"),$(this).parent().find(".sidebar-submenu").first().slideUp({done:function(){$(this).removeClass("sidebar-submenu__open")}})):($(this).find(".side-menu__sub-icon").addClass("transform rotate-180"),$(this).addClass("side-menu--open"),$(this).parent().find(".sidebar-submenu").first().slideDown({done:function(){$(this).addClass("sidebar-submenu__open")}})))})),$(".select2-basic").select2(),$(".select2-multi-select").select2(),$(".select2-auto-tokenize").select2({tags:!0,tokenSeparators:[","]}),$(document).ready((function(){$("table.default-data-table").DataTable(),$("#scroll-vertical-datatable").DataTable({scrollY:"300px",scrollCollapse:!0,paging:!1}),$("#buttons-datatable").DataTable({dom:"Bfrtip",buttons:["copy","csv","excel","pdf","print"]})})),$(".profilePicUpload").on("change",(function(){proPicURL(this)})),$(".remove-image").on("click",(function(){$(this).parents(".profilePicPreview").css("background-image","none"),$(this).parents(".profilePicPreview").removeClass("has-image"),$(this).parents(".thumb").find("input[type=file]").val("")})),$("form").on("change",".file-upload-field",(function(){$(this).parent(".file-upload-wrapper").attr("data-text",$(this).val().replace(/.*(\/|\\)/,""))}));