function replaceBootstrapCol(element, oldSize, newSize) {
	element.removeClass('col-xs-' + oldSize);
	element.removeClass('col-sm-' + oldSize);
	element.removeClass('col-md-' + oldSize);
	element.removeClass('col-lg-' + oldSize);

	element.addClass('col-xs-' + newSize);
	element.addClass('col-sm-' + newSize);
	element.addClass('col-md-' + newSize);
	element.addClass('col-lg-' + newSize);
}