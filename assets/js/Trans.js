import Localization from './Localization';

var Localization = new Localization();


function trans(trans_id,args,locale)
{
    Localization.trans(trans_id,args,locale)
}

trans.load=function(data,lang)
{
    Localization.load(data,lang);
};
trans.choice=function(trans_id,quantity,args){
    return Localization.trans_choice(trans_id,quantity,args)
}

export default trans;
