//--------------init--------------
function init() {

    //--------------初始请求数据------------
    adviceConfig.data.page = pages.advice++;
    adviceConfig.data.type = 0;
    more(adviceConfig);

    evalConfig.data.page = pages.eval++;
    evalConfig.data.type = 0;
    more(evalConfig);

    //-------------下一页---------------------

    $(".more,.btn-tab").click(function(evt) {

        var type = evt.target.dataset.type;

        switch (type) {
            case "advice":
                adviceConfig.data.page = pages.advice++;
                adviceConfig.data.type = 0;
                adviceConfig.success = appendAdvice;
                adviceConfig.type = "advice";
                more(adviceConfig);
                break;
            case "eval":
                evalConfig.data.page = pages.eval++;
                evalConfig.data.type = 0;
                evalConfig.success = appendEval;
                evalConfig.type = "eval";
                more(evalConfig);
                break;
            case "passedAdvice":
                adviceConfig.data.page = pages.passedAdvice++;
                adviceConfig.data.type = 1;
                adviceConfig.success = appendPassAdvice;
                adviceConfig.type = "passedAdvice";
                more(adviceConfig);
                break;
            case "delAdvice":
                adviceConfig.data.page = pages.delAdvice++;
                adviceConfig.data.type = -1;
                adviceConfig.success = appendDelAdvice;
                adviceConfig.type = "delAdvice";
                more(adviceConfig);
                break;
            case "passedEval":
                evalConfig.data.page = pages.passedEval++;
                evalConfig.data.type = 1;
                evalConfig.success = appendPassEval;
                evalConfig.type = "passedEval";
                more(evalConfig);
                break;
            case "delEval":
                evalConfig.data.page = pages.delEval++;
                evalConfig.data.type = -1;
                evalConfig.success = appendDelEval;
                evalConfig.type = "delEval";
                more(evalConfig);
                break;
        }

    });

    function more(config) {

        $.get(config.url, config.data, function(data, textStatus) {
            if (data.status == 1) {
                if (data.info.length == 0) {
                    $("#btn-" + config.type).removeClass("btn-success").addClass("btn-danger").html("<span class=\"glyphicon glyphicon-chevron-up\">..已加载完毕（部分新修改内容需刷新显示）.." + "<span class=\"glyphicon glyphicon-chevron-up\">")
                }
                if (config.data.type == 0)
                    data.info.length == 10 ? $('#' + config.type + 's').html("10+") : $('#' + config.type + 's').html(data.info.length);
                for (var i = 0; i < data.info.length; i++) {
                    config.success(data, i);
                }
            } else {
                alert(data);
                window.location.href = '/admin/login.html';
            }
        });

    }

    //-------------logout---------------------
    $("#logout").click(function() {
        $.post('/index.php/Admin/Admin/logout', function(data, textStatus) {
            if (data.status) {
                window.location.href = '/admin/login.html';
            } else {
                alert(data.info);
            }
        })
    })

    //-------------logout---------------------
    $("#updatePassword").click(function() {
        var currentPwd = $('#password').val();
        var pass01 = $('#password01').val();
        var pass02 = $('#password02').val();

        if (pass01 == pass02) {
            $.ajax({
                url: "/index.php/Admin/Admin/password",
                type: "PUT",
                data: {
                    currentPwd: currentPwd,
                    password: pass01
                },
                success: function(data, textStatus) {
                    if (data.status == 1) {
                        $("#tip").html("修改成功，请重新登录...").removeClass("text-warning").addClass('text-success');
                        setTimeout(function() {
                            window.location.href = "/admin/login.html";
                        }, 2000);
                    } else {
                        $("#tip").html(data.info).addClass('text-warning');
                    }
                }
            })
        } else {
            $("#tip").html("两次输入密码不一致，请重新输入...").addClass('text-warning');
        }

    })

    //-------------addAdmin---------------
    $("#addAdminBtn").click(function(evt) {
        var _account = $("#addAdminAccount").val();
        var pwd = $("#addAdminPwd").val();
        var pwd01 = $("#addAdminPwd01").val();
        var pwd02 = $("#addAdminPwd02").val();

        if (pwd01 == pwd02) {
            $.post("/index.php/Admin/Admin/index", {
                account: _account,
                currentPwd: pwd,
                password: pwd01
            }, function(data, textStatus) {
                if (data.status == 1) {
                    $("#addAdminTip").html("添加账户成功，请牢记账户与密码...").removeClass("text-warning").addClass('text-success');
                } else if(data.status == 0){
                    $("#addAdminTip").html(data.info).removeClass("text-success").addClass('text-warning');
                } else{
                    $("#addAdminTip").html("添加失败");
                }
            })
        } else {
            $("#addAdminTip").html("两次输入密码不一致，请重新输入...").addClass("text-warning");
        }
    })

    //----------addClass------------------
    $(".addClassSearch").click(function(evt) {
        var type = evt.target.dataset.type;
        var val = $("#" + type + "Name").val();
        var url = "/index.php/Admin/" + type + "/index";

        $.get(url, { key: val }, function(data, textStatus) {

            if (data.status == 1) {
                if (type == "Course") {
                    $("#courseTab").css({
                            display: "none"
                        });

                    $("#courseTbody").empty();
                    $("#" + type + "Tip").html("请选择正确的课程！如不存在请在下方添加！").addClass("text-warning");
                    $("#courseTab").css({
                        display: "block"
                    });
                    for (var i = 0; i < data.info.length; i++) {
                        $("#courseTbody").append(
                            "<tr><td><input class=\"course_check\" value=\"" + data.info[i].id + "\" type=\"radio\" name=\"courseRadio\"></td>"
                            + "<td>" + data.info[i].name + "</td><td>" + data.info[i].number + "</td></tr>");
                    }

                } else if (type == "Teacher") {

                    //清除原有结果
                    $("#teacherTbody").empty();
                    $("#" + type + "Tip").html("请选择正确的教师！如不存在请在下方添加！").addClass("text-warning");
                    $("#teacherTab").css({
                        display: "block"
                    });
                    for (var i = 0; i < data.info.length; i++) {
                        $("#teacherTbody").append(
                            //"<tr><td><input class=\"teacher_check\" value=\"" + data.info[i].id + "\" type=\"checkbox\"></td>" + "<td>" + data.info[i].name + "</td>" + "<td>" + data.info[i].number + "</td>" + "<td>" + data.info[i].department_id + "</td>" + "<td>" + data.info[i].time + "</td></tr>");
                            "<tr><td><input class=\"teacher_check\" value=\"" + data.info[i].id + "\" type=\"radio\" name=\"teacherRadio\"></td>" 
                            + "<td>" + data.info[i].name + "</td><td>" + data.info[i].number + "</td></tr>");
                    }

                }
            } else {
                $("#" + type + "Tip").html( type + "不存在，请在下方添加...").addClass("text-warning");
                if(type == "Course"){
                    $("#courseTbody").empty();
                } else {
                    $("#teacherTbody").empty();
                }
            }
            if(type == "Course"){
                if(!addClass.addCourse){
                    //$("#CourseTip").html("若不存在，请在下方添加...").addClass("text-warning");
                    $("#addClassCourse").append(
                        "<div class=\"form-group\"> <input id=\"addCourseName\" class=\"form-control addClassIpt\" placeholder=\"输入课程名\" type=\"text\"></div>" 
                        + "<div class=\"form-group\"> <input id=\"addCourseNum\" class=\"form-control addClassIpt\" placeholder=\"输入课编号\" type=\"text\"></div>" 
                        + "<div class=\"form-group\"> <button type=\"button\" class=\"btn btn-addClassAdd- btn-default \" data-type=\"Course\" style=\"width: 100%\">若 不 存 在 点 击 添 加</button></div>");
                    addClass.addCourse = true;
                }
            } else if (type == "Teacher"){

                if(!addClass.addTeacher){
                    //$("#TeacherTip").html("若不存在，请在下方添加...").addClass("text-warning");

                    $("#addClassTecher").append(
                        "<div class=\"form-group\"> <input id=\"addTeacherName\" class=\"form-control addClassIpt\" placeholder=\"输入教师姓名\" type=\"text\"></div>"
                        + "<div class=\"form-group\"> <input id=\"addTeacherNum\" class=\"form-control addClassIpt\" placeholder=\"输入教师职工号(8位以内)\" type=\"text\"></div>"
                        + "<div class=\"form-group\"> <button type=\"button\" class=\"btn btn-addClassAdd- btn-default \" data-type=\"Teacher\" style=\"width: 100%\">若 不 存 在 点 击 添 加</button></div>"

                         );
                    addClass.addTeacher = true;
                }
            }

        })
    })

    //--------------addCourse---------
    $("#addClass").on("click", ".btn-addClassAdd-", function(evt) {
        if (evt.target.dataset.type == "Course") {

            $.post("/index.php/Admin/Course/index", {
                name: $("#addCourseName").val(),
                number: $("#addCourseNum").val()
            }, function(data, textStatus) {
                if (data.status == 1) {
                    addClass.course_id = data.info;
                    $("#CourseTip").html("添加成功！已选择..").removeClass("text-warning").addClass("text-success");
                } else {
                    $("#CourseTip").html("添加失败！编号重复！").removeClass("text-success").addClass("text-warning");
                }
            });

        } else {

            $.post("/index.php/Admin/Teacher/index", {
                name: $("#addTeacherName").val(),
                number: $("#addTeacherNum").val()
            }, function(data, textStatus) {
                if (data.status == 1) {
                    addClass.teacher_id = data.info;
                    $("#TeacherTip").html("添加成功！已选择..").removeClass("text-warning").addClass("text-success");
                } else {
                    $("#TeacherTip").html("添加失败！职工号重复！").removeClass("text-success").addClass("text-warning");
                }
            });
        }
    })

    
    

    //-------------addClass-----------
    $("#btn-addClass").on('click', function() {

        var courseID = $("[name='courseRadio']").filter(":checked").val();
        var teacherID = $("[name='teacherRadio']").filter(":checked").val();

        if (teacherID) {

            addClass.teacher_id = teacherID;

        }
        if (courseID) {

            addClass.course_id = courseID;

        }

        if (addClass.teacher_id && addClass.course_id) {

            $.post("/index.php/Admin/Class/index", addClass, function(data, textStatus) {
                if (data.status == 1) {
                    $("#addClassTip").html("Class添加成功!").removeClass("text-warning").addClass("text-success");
                } else {
                    $("#addClassTip").html("添加失败！").removeClass("text-success").addClass("text-warning");
                }
            });

        } else {

            $("#TeacherTip").html("数据不完整！").removeClass("text-success").addClass("text-warning");

        }

    })

    //----------advice eval 切换----------

    $(".btn-tab").click(function(evt) {
        var div = evt.target.dataset.div;

        $('.tabContainer').removeClass("show");
        $("#" + div).addClass("show");
    });

    //--------------advice 审核 click--------------
    $("#adviceTbody, #delAdviceTbody").on('click', '.btn-advice-pass', function(evt) {
        var id = evt.target.dataset.id;
        var _status = evt.target.dataset.status;
        var td = evt.target.dataset.td;

        var url = "/index.php/Admin/Advice/" + id + "/check";
        $.post(url, { status: _status }, function(data, textStatus) {
            if (data.status) {
                $("#" + td).html("<p class=\"text-success\">操作已完成!</p>");
            }
        });
    });

    $("#adviceTbody, #passedAdviceTbody").on('click', '.btn-advice-del', function(evt) {
        var id = evt.target.dataset.id;
        var _status = evt.target.dataset.status;
        var td = evt.target.dataset.td;

        var url = "/index.php/Admin/Advice/" + id + "/check";
        $.post(url, { status: _status }, function(data, textStatus) {
            if (data.status) {
                $("#" + td).html("<p class=\"text-success\">操作已完成!</p>");
            }
        });
    });

    //--------------eval 审核 click--------------
    $("#evalTbody, #delEvalTbody").on('click', '.btn-eval-pass', function(evt) {

        var id = evt.target.dataset.id;
        var _status = evt.target.dataset.status;
        var td = evt.target.dataset.td;

        var url = "/index.php/Admin/Evaluation/" + id + "/check";
        $.post(url, { status: _status }, function(data, textStatus) {
            if (data.status) {
                $("#" + td).html("<p class=\"text-success\">操作已完成!</p>");
            }
        });

    });

    $("#evalTbody, #passedEvalTbody").on('click', '.btn-eval-del', function(evt) {

        var id = evt.target.dataset.id;
        var _status = evt.target.dataset.status;
        var td = evt.target.dataset.td;

        var url = "/index.php/Admin/Evaluation/" + id + "/check";
        $.post(url, { status: _status }, function(data, textStatus) {
            if (data.status) {
                $("#" + td).html("<p class=\"text-success\">操作已完成!</p>");
            }
        });

    });

}

    //search eval------------------------
    $('#searchEvalBt').click(function(evt){
        var num = $('#searchEvalIpt').val();
        var urlEval = "/index.php/Admin/Student/Evaluation/";
        var urlAdvice = "/index.php/Admin/Student/Advice/";

        var searchEvalTbody = $("#searchEvalTbody");
        var searchAdviceTbody = $("#searchAdviceTbody");
        var searchEvalTip = $("#searchEvalTip");
        var searchAdviceTip = $("#searchAdviceTip");

        searchEvalTbody.empty();
        searchEvalTip.empty();
        searchAdviceTbody.empty();
        searchAdviceTip.empty();

        $.get(urlEval, { number: num }, function(data, textStatus) {
            if (data.status) {
                var evals = data.info;
                var len = evals.length;

                if(len == 0){
                    
                    searchEvalTip.append('无评论或学号不存在！');
                }

                for(var i = 0; i < len; i++){
                    searchEvalTbody.append('<tr><td>'
                        + evals[i].number + '</td><td>'
                        + evals[i].name + '</td><td>' 
                        + evals[i].course + '</td><td>'
                        + evals[i].rank + '</td><td>'
                        + evals[i].comment + '</td><td>'
                        + evals[i].time + '</td><td>'
                        + ((evals[i].status == 1)?"已通过" : "待审核")
                        +'</td></tr>');
                }
            } else {
                searchEvalTip.append('请输入学号！');
            }
        });

        $.get(urlAdvice, { number: num }, function(data, textStatus) {
            if (data.status) {
                var advices = data.info;
                var len = advices.length;

                if(len == 0){
                    searchAdviceTip.append('无建议或学号不存在！');
                }

                for(var i = 0; i < len; i++){
                    searchAdviceTbody.append('<tr><td>'
                        + advices[i].number + '</td><td>'
                        + advices[i].name + '</td><td>' 
                        + advices[i].content + '</td><td>'
                        + advices[i].humanity + '</td><td>'
                        + advices[i].social + '</td><td>'
                        + advices[i].technology + '</td><td>'
                        + advices[i].practice + '</td><td>'
                        + advices[i].other + '</td><td>'
                        + advices[i].useful + '</td><td>'
                        + advices[i].time + '</td><td>'
                        + ((advices[i].status == 1)?"已通过" : "待审核")
                        +'</td></tr>');
                }
            } else {
                searchAdviceTip.append('请输入学号！');
            }
        });
    
        $('.tabContainer').removeClass("show");
        $("#searchEvalDiv").addClass("show");
        $("#searchAdviceDiv").addClass("show");
    });

//---------------未审核 添加行-------------
function appendAdvice(data, i) {
    var tr = document.createElement('tr');
    var content = document.createElement('td');
    var technology = document.createElement('td');
    var art = document.createElement('td');
    var other = document.createElement('td');
    var humanity = document.createElement('td');
    var social = document.createElement('td');
    var practice = document.createElement('td');
    var time = document.createElement('td');
    var useful = document.createElement('td');
    var action = document.createElement('td');
    action.setAttribute('id', 'action-advice-' + data.info[i].id);
    var btnPass = document.createElement('button');
    btnPass.setAttribute('class', "btn btn-default btn-advice-pass");
    btnPass.setAttribute('data-id', data.info[i].id);
    btnPass.setAttribute('data-status', 1);
    btnPass.setAttribute('data-td', 'action-advice-' + data.info[i].id);
    btnPass.innerHTML = "通过";
    var btnReject = document.createElement('button');
    btnReject.setAttribute('class', "btn btn-danger btn-advice-del");
    btnReject.setAttribute('data-id', data.info[i].id);
    btnReject.setAttribute('data-status', -1);
    btnReject.setAttribute('data-td', 'action-advice-' + data.info[i].id);
    btnReject.innerHTML = "屏蔽";

    content.appendChild(document.createTextNode(data.info[i].content));
    technology.appendChild(document.createTextNode(data.info[i].technology));
    art.appendChild(document.createTextNode(data.info[i].art));
    other.appendChild(document.createTextNode(data.info[i].other));
    humanity.appendChild(document.createTextNode(data.info[i].humanity));
    social.appendChild(document.createTextNode(data.info[i].social));
    practice.appendChild(document.createTextNode(data.info[i].practice));
    time.appendChild(document.createTextNode(data.info[i].time));
    useful.appendChild(document.createTextNode(data.info[i].useful));
    action.appendChild(btnPass);
    action.appendChild(btnReject);


    tr.appendChild(content);
    tr.appendChild(technology);
    tr.appendChild(art);
    tr.appendChild(other);
    tr.appendChild(humanity);
    tr.appendChild(social);
    tr.appendChild(practice);
    tr.appendChild(time);
    tr.appendChild(useful);
    tr.appendChild(action);

    //tbody.appendChild(tr);
    adviceTbody.appendChild(tr);
}

function appendEval(data, i) {
    var tr = document.createElement('tr');
    var comment = document.createElement('td');
    var rank = document.createElement('td');
    var why = document.createElement('td');
    var time = document.createElement('td');
    var action = document.createElement('td');
    action.setAttribute('id', 'action-eval-' + data.info[i].id);
    var btnPass = document.createElement('button');
    btnPass.setAttribute('class', "btn btn-default btn-eval-pass");
    btnPass.setAttribute('data-id', data.info[i].id);
    btnPass.setAttribute('data-status', 1);
    btnPass.setAttribute('data-td', 'action-eval-' + data.info[i].id);
    btnPass.innerHTML = "通过";
    var btnReject = document.createElement('button');
    btnReject.setAttribute('class', "btn btn-danger btn-eval-del");
    btnReject.setAttribute('data-id', data.info[i].id);
    btnReject.setAttribute('data-status', -1);
    btnReject.setAttribute('data-td', 'action-eval-' + data.info[i].id);
    btnReject.innerHTML = "屏蔽";

    comment.appendChild(document.createTextNode(data.info[i].comment));
    rank.appendChild(document.createTextNode(data.info[i].rank));
    why.appendChild(document.createTextNode(data.info[i].why));
    time.appendChild(document.createTextNode(data.info[i].time));
    action.appendChild(btnPass);
    action.appendChild(btnReject);


    tr.appendChild(comment);
    tr.appendChild(rank);
    tr.appendChild(why);
    tr.appendChild(time);
    tr.appendChild(action);

    //tbody.appendChild(tr);
    evalTbody.appendChild(tr);
}

//-------------------已审核 添加行-----------------
function appendPassAdvice(data, i) {
    var tr = document.createElement('tr');
    var content = document.createElement('td');
    var technology = document.createElement('td');
    var art = document.createElement('td');
    var other = document.createElement('td');
    var humanity = document.createElement('td');
    var social = document.createElement('td');
    var practice = document.createElement('td');
    var time = document.createElement('td');
    var useful = document.createElement('td');
    var action = document.createElement('td');
    action.setAttribute('id', 'action-advice-passToDel-' + data.info[i].id);

    var btnReject = document.createElement('button');
    btnReject.setAttribute('class', "btn btn-danger btn-advice-del");
    btnReject.setAttribute('data-id', data.info[i].id);
    btnReject.setAttribute('data-status', -1);
    btnReject.setAttribute('data-td', "action-advice-passToDel-" + data.info[i].id);
    btnReject.innerHTML = "屏蔽";

    content.appendChild(document.createTextNode(data.info[i].content));
    technology.appendChild(document.createTextNode(data.info[i].technology));
    art.appendChild(document.createTextNode(data.info[i].art));
    other.appendChild(document.createTextNode(data.info[i].other));
    humanity.appendChild(document.createTextNode(data.info[i].humanity));
    social.appendChild(document.createTextNode(data.info[i].social));
    practice.appendChild(document.createTextNode(data.info[i].practice));
    time.appendChild(document.createTextNode(data.info[i].time));
    useful.appendChild(document.createTextNode(data.info[i].useful));
    action.appendChild(btnReject);


    tr.appendChild(content);
    tr.appendChild(technology);
    tr.appendChild(art);
    tr.appendChild(other);
    tr.appendChild(humanity);
    tr.appendChild(social);
    tr.appendChild(practice);
    tr.appendChild(time);
    tr.appendChild(useful);
    tr.appendChild(action);

    //tbody.appendChild(tr);
    passedAdviceTbody.appendChild(tr);
}

function appendDelAdvice(data, i) {
    var tr = document.createElement('tr');
    var content = document.createElement('td');
    var technology = document.createElement('td');
    var art = document.createElement('td');
    var other = document.createElement('td');
    var humanity = document.createElement('td');
    var social = document.createElement('td');
    var practice = document.createElement('td');
    var time = document.createElement('td');
    var useful = document.createElement('td');
    var action = document.createElement('td');
    action.setAttribute('id', 'action-advice-delToPass-' + data.info[i].id);
    var btnPass = document.createElement('button');
    btnPass.setAttribute('class', "btn btn-default btn-advice-pass");
    btnPass.setAttribute('data-id', data.info[i].id);
    btnPass.setAttribute('data-status', 1);
    btnPass.setAttribute('data-td', 'action-advice-delToPass-' + data.info[i].id);
    btnPass.innerHTML = "通过";

    content.appendChild(document.createTextNode(data.info[i].content));
    technology.appendChild(document.createTextNode(data.info[i].technology));
    art.appendChild(document.createTextNode(data.info[i].art));
    other.appendChild(document.createTextNode(data.info[i].other));
    humanity.appendChild(document.createTextNode(data.info[i].humanity));
    social.appendChild(document.createTextNode(data.info[i].social));
    practice.appendChild(document.createTextNode(data.info[i].practice));
    time.appendChild(document.createTextNode(data.info[i].time));
    useful.appendChild(document.createTextNode(data.info[i].useful));
    action.appendChild(btnPass);


    tr.appendChild(content);
    tr.appendChild(technology);
    tr.appendChild(art);
    tr.appendChild(other);
    tr.appendChild(humanity);
    tr.appendChild(social);
    tr.appendChild(practice);
    tr.appendChild(time);
    tr.appendChild(useful);
    tr.appendChild(action);

    //tbody.appendChild(tr);
    delAdviceTbody.appendChild(tr);
}

function appendPassEval(data, i) {
    var tr = document.createElement('tr');
    var comment = document.createElement('td');
    var rank = document.createElement('td');
    var why = document.createElement('td');
    var time = document.createElement('td');
    var action = document.createElement('td');
    action.setAttribute('id', 'action-eval-passToDel-' + data.info[i].id);
    var btnReject = document.createElement('button');
    btnReject.setAttribute('class', "btn btn-danger btn-eval-del");
    btnReject.setAttribute('data-id', data.info[i].id);
    btnReject.setAttribute('data-status', -1);
    btnReject.setAttribute('data-td', 'action-eval-passToDel-' + data.info[i].id);
    btnReject.innerHTML = "屏蔽";

    comment.appendChild(document.createTextNode(data.info[i].comment));
    rank.appendChild(document.createTextNode(data.info[i].rank));
    why.appendChild(document.createTextNode(data.info[i].why));
    time.appendChild(document.createTextNode(data.info[i].time));
    action.appendChild(btnReject);


    tr.appendChild(comment);
    tr.appendChild(rank);
    tr.appendChild(why);
    tr.appendChild(time);
    tr.appendChild(action);

    //tbody.appendChild(tr);
    passedEvalTbody.appendChild(tr);
}

function appendDelEval(data, i) {
    var tr = document.createElement('tr');
    var comment = document.createElement('td');
    var rank = document.createElement('td');
    var why = document.createElement('td');
    var time = document.createElement('td');
    var action = document.createElement('td');
    action.setAttribute('id', 'action-eval-delToPass-' + data.info[i].id);
    var btnPass = document.createElement('button');
    btnPass.setAttribute('class', "btn btn-default btn-eval-pass");
    btnPass.setAttribute('data-id', data.info[i].id);
    btnPass.setAttribute('data-status', 1);
    btnPass.setAttribute('data-td', 'action-eval-delToPass-' + data.info[i].id);
    btnPass.innerHTML = "通过";

    comment.appendChild(document.createTextNode(data.info[i].comment));
    rank.appendChild(document.createTextNode(data.info[i].rank));
    why.appendChild(document.createTextNode(data.info[i].why));
    time.appendChild(document.createTextNode(data.info[i].time));
    action.appendChild(btnPass);

    tr.appendChild(comment);
    tr.appendChild(rank);
    tr.appendChild(why);
    tr.appendChild(time);
    tr.appendChild(action);

    //tbody.appendChild(tr);
    delEvalTbody.appendChild(tr);
}
