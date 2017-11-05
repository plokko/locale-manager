
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

export default trans;