
function trans(trans_id,args)
{
    let tr = trans.get(trans_id);
    if(!tr)
        return trans_id;

    // Replace placeholders
    if(args && (typeof tr ==='string')) {
        for(let k in args) {
            tr = tr.split(':'+k).join(args[k]);
        }
    }
    return tr;
}
trans.get=function(trans_id,lang){
    if(!window.trans || !window.trans.messages)
        return null;

    let pointer=window.trans.messages;
    for(let k of trans_id.split('.'))
    {
        if(pointer[k] === undefined)
            return null;//Trans not found!
        pointer = pointer[k];
    }
    return pointer;
}
trans.load=function(data,lang)
{
    window.trans.messages = data;
    window.trans.lang = lang;
};
trans.choice=function(trans_id,quantity,args){
    let tr = trans.get(trans_id);
    if(tr && (typeof tr ==='string')){
        var rgx = /^(?:{(\d+)}|\[(\d+),(\d+|\*)\]) (.*)$/im;

        let list = tr.split('|');
        for(let t of list)
        {
            var match = rgx.exec(t);
            if (match != null) {
                if((!isNaN(match[1]) && match[1]==number) ||
                    (
                        (match[2]=='*' || (!isNaN(match[2]) && match[2]<=number)) &&
                        (match[3]=='*' || (!isNaN(match[3]) && match[3]>=number))
                    )
                    ){
                    // Replace placeholders
                    t = match[4];//get only matched text
                    if(args) {
                        for(let k in args) {
                            t = t.split(':'+k).join(args[k]);
                        }
                    }
                    return t;
                }
            }

        }
        return (number>1 && list.length>1)?list[1]:list[0];
    }
    return tr||trans_id;
}

export default trans;
