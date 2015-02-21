function checkAll()
{
	var checkboxes = $(this).parents().eq(3).find('tbody > tr > td:first-child > :input');
	checkboxes.prop('checked',$(this).prop('checked'));
}
function spyCheck()
{	//Changement du bouton checkAll
	var checkAll = $(this).parents('table').find('tfoot > tr > td:first-child > :input');
	var checkboxes = $(this).parents().eq(2).find('tr > td:first-child > :input');
	var checked = true;
	checkboxes.each(function(e){
		checked = ($(this).prop('checked')&&checked);
	});
	checkAll.prop('checked',checked);
	//Reset des inputs
	var inputs = $(this).parent().parent().find(':input:not(:first)');
	if(!$(this).prop('checked')){
		inputs.each(function(e){
			if($(this).attr('type')=='text'){
				$(this).val($(this).attr('placeholder'));
			}else if($(this).attr('type')=='checkbox'){
				$(this).prop('checked',($(this).attr('value')=='Oui'));
			}else if($(this).attr('type')=='date'){
				$(this).val($(this).attr('value'));
			}
		});
	}
}
function checkToMod()
{
	var checkbox = $(this).parent().parent().find(':input:first');
	var inputs = $(this).parent().parent().find(':input:not(:first)');
	var modif = false;
	inputs.each(function(e){
		if($(this).attr('type')=='text'){
			if($(this).val() != $(this).attr('placeholder')){
				modif = true;
			}
		}else if($(this).attr('type')=='checkbox'){
			if($(this).prop('checked') != ($(this).attr('value')=='Oui')){
				modif = true;
			}
		}else if($(this).attr('type')=='date'){
			if($(this).val() != $(this).attr('value')){
				modif = true;
			}
		}
	});
	checkbox.prop('checked',modif);
}
function switchOn(){
	//changement de l'action du bouton
	$(this).one('change',switchOff);
	//recuperation des elements du formulaire
	var parentForm = $(this).parents("form:first");
	var page = parentForm.attr('id');
	var headLine = parentForm.find('thead > tr');
	//Modification de la table
	headLine.prepend($("<th></th>"));
	parentForm.find(".current").prepend($('<td></td>'));
	parentForm.find("tbody > tr:not(.current)").each(function(e){
		var id = $(this).children('td:first-child').text();
		$(this).prepend('<td><input class="no-margin" type="checkbox" name="'+page+'_toMod[]" value="'+id+'" /></td>');
		//On remplace les element de classe "linkToSubmit" en submit
		if($(this).find('.linkToSubmit')){
			$(this).find('.linkToSubmit').html('<input class="button tiny no-margin" type="submit" name="'+page+'_modif_'+id+'" id="'+page+'_modif_'+id+'" value="Modifier" />');
		}
		//On remplace les elements de classe "textToInput" en Input
		if($(this).find('.textToInput')){
			$(this).find('.textToInput').each(function(e){
				var type = headLine.children('th:eq('+$(this).index()+')').text().replace(' ','_');
				var info = $(this).text();
				$(this).html('<input type="text" class="no-margin" name="'+page+'_'+type+'_'+id+'" id="'+page+'_'+type+'_'+id+'" placeholder="'+info+'" value="'+info+'"/>');
				$(this).children().on("keyup",checkToMod);
			});
		}
		//On remplace les elements de class textToDate en Input
		if($(this).find('.textToDate')){
			$(this).find('.textToDate').each(function(e){
				var type = headLine.children('th:eq('+$(this).index()+')').text().replace(' ','_');
				$(this).html('<input type="date" class="no-margin" value="'+$(this).text()+'" name="'+page+'_'+type+'_'+id+'" id="'+page+'_'+type+'_'+id+'" />');
				$(this).children().on("blur",checkToMod);
			});
		}
		//On remplace les elements de class textToCheckbox en Input 
		if($(this).find('.textToCheckbox')){
			$(this).find('.textToCheckbox').each(function(e){
				var type = headLine.children('th:eq('+$(this).index()+')').text().replace(' ','_');
				var checked = ($(this).text()=='Oui');
				$(this).html('<input type="checkbox" class="no-margin" value="'+$(this).text()+'" name="'+page+'_'+type+'_'+id+'" id="'+page+'_'+type+'_'+id+'"/>');
				$(this).children().prop('checked',checked);
				$(this).children().on('change',checkToMod);
			});
		}
	});
	parentForm.find(':input[name="'+page+'_toMod[]"]').on('change',spyCheck);
	//Ligne de validation du formulaire
	var colspan = (parentForm.find('thead > tr:first > th').length);
	parentForm.find('table').append('<tfoot><tr><td><input type="checkbox" class="no-margin" name="'+page+'_check_all"  /></td><td colspan="'+colspan+'"><input type="submit" class="button small expand no-margin" value="Modifier la selection" name="'+page+'_modif_all"/></td></tr></tfoot>')
	parentForm.find(':input[name="'+page+'_check_all"]').on('change',checkAll);
}

function switchOff(){
	//Changement de l'action du bouton
	$(this).one('change',switchOn);
	//recuperation des elements du formulaire
	var parentForm = $(this).parents("form:first");
	var page = parentForm.attr('id');
	var headLine = parentForm.find('thead > tr');
	//Modifications de la table
	headLine.children('th:first-child').remove();
	parentForm.find('.current').find('td:first-child').remove();
	parentForm.find("tbody > tr:not(.current)").each(function(e){
		$(this).children('*:first-child').remove();
		var id = $(this).children('td:first-child').text();
		//Transformation des elements de classe "linkToSubmit" en lien
		if($(this).find('.linkToSubmit')){
			$(this).find('.linkToSubmit').html($('<a href="/'+page+'/edit/'+id+'">Modifier</a>'));
		}
		//On remplace les elements de classe "textToInput" en texte
		if($(this).find('.textToInput')){
			$(this).find('.textToInput').each(function(e){
				$(this).html($(this).children().attr('placeholder'));
			});
		}
		//On remplace les elements de class textToDate en texte
		if($(this).find('.textToDate')){
			$(this).find('.textToDate').each(function(e){
				$(this).html($(this).children().attr('value'));
			})
		}
		//On remplace les elements de class textToCheckbox en texte
		if($(this).find('.textToCheckbox')){
			$(this).find('.textToCheckbox').each(function(e){
				var checked = $(this).children().attr('value');
				$(this).html(checked);
			});
		}
	});
	//ligne de validation du formulaire
	parentForm.find("tfoot").remove();
}

$(document).ready(function(){
	$(".switchQuickMod").one("change",switchOn);
});