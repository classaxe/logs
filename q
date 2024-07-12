warning: in the working copy of 'resources/views/components/logs-map.blade.php', LF will be replaced by CRLF the next time Git touches it
warning: in the working copy of 'resources/views/components/logs-tabs.blade.php', LF will be replaced by CRLF the next time Git touches it
[1mdiff --git a/resources/css/logs.css b/resources/css/logs.css[m
[1mindex 6a22787..34c20e5 100644[m
[1m--- a/resources/css/logs.css[m
[1m+++ b/resources/css/logs.css[m
[36m@@ -1,3 +1,4 @@[m
[32m+[m[32m/* Loading Shroud */[m
 .overlay{[m
     display: none;[m
     position: fixed;[m
[36m@@ -8,11 +9,9 @@[m [m.overlay{[m
     z-index: 999;[m
     background: rgba(255,255,255,0.8) url("$images/loader.gif") center no-repeat;[m
 }[m
[31m-/* Turn off scrollbar when body element has the loading class */[m
 body.loading{[m
     overflow: hidden;[m
 }[m
[31m-/* Make spinner image visible when body element has the loading class */[m
 body.loading .overlay{[m
     display: block;[m
 }[m
[36m@@ -20,12 +19,57 @@[m [mbody.loading .overlay{[m
 div.group {[m
     display: inline-block; white-space: nowrap; margin-bottom: 0.25em;[m
 }[m
[31m-table.list { border-collapse: collapse; font-size: 80%; margin: 0 auto; }[m
[32m+[m[32m#tabs {[m
[32m+[m[32m    margin: 0.5em 0 0 0;[m
[32m+[m[32m}[m
[32m+[m[32m#tabs .button {[m
[32m+[m[32m    color: #fff;[m
[32m+[m[32m    font-weight: bold;[m
[32m+[m[32m    text-decoration: none;[m
[32m+[m[32m    border-radius: 5px 5px 0 0;[m
[32m+[m[32m    padding: 0.5em 0.75em;[m
[32m+[m[32m    border-bottom: none;[m
[32m+[m[32m    cursor: pointer;[m
[32m+[m[32m    border: #008;[m
[32m+[m[32m    margin: 0 0.25em 0 0;[m
[32m+[m[32m}[m
[32m+[m[32m#tabs .button span {[m
[32m+[m[32m    text-decoration: underline;[m
[32m+[m[32m}[m
[32m+[m[32m#tabs .button.is-active {[m
[32m+[m[32m    background: #4545e6;[m
[32m+[m[32m    color: yellow !important;[m
[32m+[m[32m}[m
[32m+[m[32m#tabs .button.is-inactive {[m
[32m+[m[32m    background: #7d7db3;[m
[32m+[m[32m}[m
[32m+[m[32m#tabs .button .tabicon {[m
[32m+[m[32m    display: inline;[m
[32m+[m[32m    width: 1.5em;[m
[32m+[m[32m    height: 1.5em;[m
[32m+[m[32m    text-decoration: none;[m
[32m+[m[32m}[m
[32m+[m[32m#tabs .button:hover span,[m
[32m+[m[32m#tabs .button.is-active span {[m
[32m+[m[32m    text-decoration: underline;[m
[32m+[m[32m}[m
[32m+[m[32m#tabs .button:hover .tabicon,[m
[32m+[m[32m#tabs .button.is-active .tabicon {[m
[32m+[m[32m    text-decoration: none;[m
[32m+[m[32m}[m
[32m+[m[32m#tabs .button.icon {[m
[32m+[m[32m    padding: 0.25em 0.75em;[m
[32m+[m[32m}[m
[32m+[m
[32m+[m[32mtable.list {[m
[32m+[m[32m    border-collapse: collapse; font-size: 80%; margin: 0 auto; width: 100%[m
[32m+[m[32m}[m
 table.list tbody tr:nth-child(odd) {[m
     background-color: #f0f8ff;[m
 }[m
 table.list thead {[m
     background: #9ca3af;[m
[32m+[m[32m    font-size: 90%;[m
     color: #fff;[m
     text-align: left;[m
     white-space: nowrap;[m
[1mdiff --git a/resources/js/logs.js b/resources/js/logs.js[m
[1mindex 1193526..a4151d5 100644[m
[1m--- a/resources/js/logs.js[m
[1m+++ b/resources/js/logs.js[m
[36m@@ -295,6 +295,18 @@[m [mwindow.addEventListener("DOMContentLoaded", function(){[m
         frm.update();[m
         $(this).blur();[m
     });[m
[32m+[m[32m    $('#show_list').click(function() {[m
[32m+[m[32m        $('#show_list').removeClass('is-inactive').addClass('is-active');[m
[32m+[m[32m        $('#show_map').removeClass('is-active').addClass('is-inactive');[m
[32m+[m[32m        $('.map').hide();[m
[32m+[m[32m        $('.list').show();[m
[32m+[m[32m    });[m
[32m+[m[32m    $('#show_map').click(function() {[m
[32m+[m[32m        $('#show_list').removeClass('is-active').addClass('is-inactive');[m
[32m+[m[32m        $('#show_map').removeClass('is-inactive').addClass('is-active');[m
[32m+[m[32m        $('.list').hide();[m
[32m+[m[32m        $('.map').show();[m
[32m+[m[32m    });[m
     var $sortable = $('.sortable');[m
     $sortable.on('click', function(){[m
         var $this = $(this);[m
[1mdiff --git a/resources/views/components/logs-map.blade.php b/resources/views/components/logs-map.blade.php[m
[1mindex e69de29..dbc942f 100644[m
[1m--- a/resources/views/components/logs-map.blade.php[m
[1m+++ b/resources/views/components/logs-map.blade.php[m
[36m@@ -0,0 +1,3 @@[m
[32m+[m[32m<div class="map" style="display: none">[m
[32m+[m[32m    Coming soon![m
[32m+[m[32m</div>[m
[1mdiff --git a/resources/views/components/logs-tabs.blade.php b/resources/views/components/logs-tabs.blade.php[m
[1mindex 3a31c3f..e9eb0ad 100644[m
[1m--- a/resources/views/components/logs-tabs.blade.php[m
[1m+++ b/resources/views/components/logs-tabs.blade.php[m
[36m@@ -1,11 +1,12 @@[m
 <div id="tabs">[m
[31m-    <a title="Listing" class="button icon is-active" id="show_list" onclick="changeShowMode('list')">[m
[32m+[m[32m    <a title="Listing" class="button icon is-active" id="show_list">[m
         <img class="tabicon" src="/images/icon_list.png" alt="list">[m
         <span class="tabtext">Listing</span>[m
     </a>[m
 [m
[31m-    <a title="Map" class="button icon is-inactive" id="show_map" onclick="changeShowMode('map')">[m
[32m+[m[32m    <a title="Map" class="button icon is-inactive" id="show_map">[m
         <img class="tabicon" src="/images/icon_map.png" alt="map">[m
         <span class="tabtext">Map</span>[m
     </a>[m
 </div>[m
[41m+[m
[1mdiff --git a/resources/views/logs.blade.php b/resources/views/logs.blade.php[m
[1mindex 989274d..ca6314d 100644[m
[1m--- a/resources/views/logs.blade.php[m
[1m+++ b/resources/views/logs.blade.php[m
[36m@@ -6,5 +6,7 @@[m
     @include('components.logs-form')[m
     @include('components.logs-stats')[m
     @include('components.logs-tips')[m
[31m-    @include('components.logs-table')[m
[32m+[m[32m    @include('components.logs-tabs')[m
[32m+[m[32m    @include('components.logs-list')[m
[32m+[m[32m    @include('components.logs-map')[m
 </x-app-layout>[m
