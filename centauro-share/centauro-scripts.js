/**
 * Created by Daniel on 07/11/14.
 */
jQuery(document).ready(centauroInitScripts);
function centauroInitScripts()
{
    jQuery(".popup-window").bind("click",itemClicked);

}
function itemClicked(e)
{
    e.preventDefault();
    window.open(e.currentTarget.href,'','height=340,width=800')
}
