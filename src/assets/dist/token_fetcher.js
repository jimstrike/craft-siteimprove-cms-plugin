(window.webpackJsonp=window.webpackJsonp||[]).push([["token_fetcher"],{asL2:function(n,e,a){},kBpc:function(n,e,a){"use strict";a.r(e);a("asL2");var o=jQuery;o(document).on("click","[data-siteimprove-token-generate]",(function(n){n.preventDefault();var e=o(this),a=e.data("siteimproveTokenGenerate");o.ajax({url:a,method:"GET",data:{},beforeSend:function(){e.attr({disabled:!0}),e.find("span").hide(0),e.find("span").eq(1).show(0)}}).done((function(n){o("#si-token-placeholder").addClass("token").val(n.token)})).fail((function(n,a){o("#si-token-placeholder").addClass("token").val(a),e.find("span").hide(0),e.find("span:first").show(0),console.log(n,a)})).always((function(){e.find("span").hide(0),e.find("span:last").show(0).css({cursor:"default"})}))}))}},[["kBpc","runtime"]]]);