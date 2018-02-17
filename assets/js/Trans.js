
function trans(trans_id,args)
{
    if(!window.trans || !window.trans.messages)
        return trans_id;

    let split=trans_id.split('.');

    let pointer=window.trans.messages;
    for(let k of split)
    {
        if(pointer[k] === undefined)
            return trans_id;//Trans not found!
        pointer = pointer[k];
    }

    if(args && (typeof pointer ==='string'))
    {
        // Replace placeholders
        for(let k in args)
        {
            pointer = pointer.split(':'+k).join(args[k]);
        }
    }

    return pointer;
}
trans.load=function(data,lang)
{
    window.trans.messages = data;
    window.trans.lang = lang;
};

export default trans;