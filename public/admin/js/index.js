$(function(){

    window.pages = {
        advice: 1,
        eval: 1,
        passedAdvice: 1,
        delAdvice: 1,
        passedEval: 1,
        delEval: 1
    };
    window.adviceConfig = {
        url: '/index.php/Admin/Advice/index',
        success: appendAdvice,
        type: 'advice',
        data: {

        }
    };
    window.evalConfig = {
        url: '/index.php/Admin/Evaluation/index',
        success: appendEval,
        type: 'eval',
        data: {

        }
    };
    window.addClass = {
        addTeacher: false,
        addCourse: false
    };
    
    var adviceTbody = document.getElementById('adviceTbody');
    var evalTbody = document.getElementById('evalTbody');

    var passedAdviceTbody = document.getElementById('passedAdviceTbody');
    var delAdviceTbody = document.getElementById('delAdviceTbody');

    var passedEvalTbody = document.getElementById('passedEvalTbody');
    var delEvalTbody = document.getElementById('delEvalTbody');
    

    init();
    


});