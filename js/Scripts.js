
// openType - три вида: view, edit, new
function openPage(page, id=-1, openType='view', title=''){

    let item = document.getElementsByName('page')[0];
    item.value = page;


    let item2 = document.getElementsByName('id_form')[0];
    item2.value = id;

    let openT = document.getElementsByName('open_type')[0];
    openT.value = openType;

    let item3 = document.getElementsByName('form_title')[0];
    item3.value = title;

    let form = document.getElementsByName('form_page')[0];
    form.submit();
    return '';
}