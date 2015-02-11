$.datepicker.setDefaults({dateFormat:"dd-mm-yy",changeMonth:true,changeYear:true,constrainInput:true,defaultDate:"-40y",yearRange:"-65:-18"})
$(window).resize(function(){
	var wrapper=$("#wrapper")
	var minHeight=$(this).height()-65
	if(wrapper.height()<minHeight){
		wrapper.css("height",minHeight)
	}
})
$(document).on("click","#logo,header .button",function(){
	$.post("",{name:$(this).data("href")},render)
}).on("focus","input[type!=hidden]",function(){
	var input=$(this)
	input.attr("class","focus")
	if(input.val()==input.data("default")){
		if(input.data("type")=="password"){
			input.attr("type","password")
		}
		input.val("")
	}
}).on("blur","input[type!=hidden]",function(){
	var input=$(this)
	input.trigger("input")
	if(!input.val()){
		input.removeAttr("class").val(input.data("default"))
		if(input.data("type")=="password"){
			input.attr("type","text")
		}
	}
}).on("input","input[type=text]",function(){
	var input=$(this)
	input.val(input.val().toUpperCase())

}).on("submit","form",function(e){
	var form=$(this),inputs=form.find("input[type!=hidden]"),error=false
	e.preventDefault()
	inputs.each(function(index){
		var input=$(this)
		if(input.val()=="" || (typeof input.data("default")=="string" && input.data("default")==input.val()) || (input.val().trim()!=input.val())){
			if(!error && input.data("ignore")!="ignore"){
				input.focus()
				error=true
			}
		}
		if(index==inputs.length-1 && !error){
			$.post("",form.serialize(),render)
		}
	})
}).ready(render())
function render(page){
	if(page){
		$("title").text($("body").html(page).find("#title").text())
	}
	$(window).resize()
	$("header .button[data-href="+$("#wrapper").attr("class")+"]").addClass("active")
	$("input[data-default]").each(function(){
		var input=$(this)
		input.val(input.data("default"))
	})
	$("input[data-type=date]").datepicker({onClose:function(){
		$(this).focus()
	}})
	$("input[data-type=auto]").each(function(){
		var input=$(this)
		input.autocomplete({appendTo:input.parent(),autoFocus:true,minLength:1,source:function(req,res){
			$.post("",{name:$("form").find("input[name=name]").val(),field:input.data("name"),term:req.term},res,"json")
		}})
	})
}
