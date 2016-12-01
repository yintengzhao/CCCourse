@ng_app.controller("SchemeCtrl", ['$controller', '$scope', '$interval', '$timeout', '$window', '$location', '$http', '$sce', 'NetManager',
($controller, $scope, $interval, $timeout, $window, $location, $http, $sce, NetManager)->
	$controller('ApplicationCtrl', {$scope: $scope});
	#------write by foreve_---------
	#$scope.able_to_comment = true

	#---------

	schemes = [
		{
			name: "计算机博士",
			type: 0,
			required: [
				{
					name: "计算机科学专题研究报告"
				},
				{
					name: "计算机科学前沿讨论班（1）"
				},
				{
					name: "计算机科学前沿讨论班（2）"
				}
			],
			elective: [
				{
					name: "分布式操作系统"
				},
				{
					name: "网络存储系统"
				},
				{
					name: "移动计算与无线网络"
				},
				{
					name: "网络与系统安全"
				},
				{
					name: "可重构系统"
				},
				{
					name: "专业英语"
				},
				{
					name: "媒体计算"
				},
				{
					name: "认知计算与数据科学导论"
				},
				{
					name: "WEB大数据挖掘"
				}
			]
		},
		{
			name: "计算学硕",
			type: 1,
			required: [
				{
					name: "信息科学前沿"
				},
				{
					name: "专业数学基础（A）"
				},
				{
					name: "计算机算法设计与分析"
				},
				{
					name: "计算机网络技术"
				},
				{
					name: "高级计算机系统结构"
				}
			],
			elective: [
				{
					name: "计算机图形与图像技术"
				},
				{
					name: "自然语言处理"
				},
				{
					name: "形式语言与自动机"
				},
				{
					name: "模式识别"
				},
				{
					name: "机器学习"
				},
				{
					name: "并行计算技术"
				},
				{
					name: "人工智能原理"
				},
				{
					name: "计算机视觉"
				},
				{
					name: "数据库系统实现"
				},
				{
					name: "网络存储系统"
				},
				{
					name: "分布式操作系统"
				},
				{
					name: "软件测试技术"
				},
				{
					name: "现代嵌入式系统"
				}
			]
		},
		{
			name: "计算专硕",
			type: 2,
			required: [
				{
					name: "专业数学基础（A）"
				},
				{
					name: "计算机网络技术"
				},
				{
					name: "计算机算法设计与分"
				},
				{
					name: "专业实践1"
				},
				{
					name: "信息检索"
				},
				{
					name: "知识产权"
				},
				{
					name: "专业实践讲座"
				},
				{
					name: "计算机技术前沿"
				},
				{
					name: "数据科学前沿"
				},
				{
					name: "计算机网络课程实践"
				},
				{
					name: "计算机算法课程实践"
				},
				{
					name: "自然辩证法"
				},
				{
					name: "政治"
				}
			],
			elective: [
				{
					name: "高级计算机系统结构"
				},
				{
					name: "模式识别"
				},
				{
					name: "机器学习"
				},
				{
					name: "计算机视觉"
				},
				{
					name: "现代嵌入式系统"
				},
				{
					name: "计算机图形与图像技术"
				},
				{
					name: "自然语言处理"
				},
				{
					name: "形式语言与自动机"
				}
			]
		}
		{
			name: "控制博士",
			type: 3,
			required: [
				{
					name: "控制科学专题研究报告（1）"
				},
				{
					name: "控制科学专题研究报告（2）"
				},
				{
					name: "运筹学与最优化"
				}
			],
			elective: [
				{
					name: "鲁棒控制理论基础"
				},
				{
					name: "自适应控制理论及应用讨论班（1）"
				},
				{
					name: "自适应控制理论及应用讨论班（2）"
				},
				{
					name: "模糊系统与控制"
				},
				{
					name: "随机过程"
				},
				{
					name: "智能预测控制"
				},
				{
					name: "微操作与虚拟现实"
				},
				{
					name: "供应链建模与物流分析讨论班"
				},
				{
					name: "泛函分析基础"
				},
				{
					name: "机器人高级技术"
				},
				{
					name: "基于李雅普诺夫函数的非线性控制"
				},
				{
					name: "机器人视觉控制"
				},
				{
					name: "三维数据场可视化讨论班"
				},
				{
					name: "生物启发计算"
				},
				{
					name: "移动机器人非线性控制"
				}
			]
		},
		{
			name: "控制学硕",
			type: 4,
			required: [
				{
					name: "信息科学前沿"
				},
				{
					name: "建模与辨识"
				},
				{
					name: "随机过程"
				},
				{
					name: "线性系统理论"
				},
				{
					name: "机器人学"
				}
			],
			elective: [
				{
					name: "专业数学基础（A）"
				},
				{
					name: "运筹学与最优化"
				},
				{
					name: "计算机网络技术"
				},
				{
					name: "计算机算法设计与分析"
				},
				{
					name: "供应链建模与物流分析讨论班"
				},
				{
					name: "自适应控制理论及应用讨论班（1）"
				},
				{
					name: "自适应控制理论及应用讨论班（2）"
				},
				{
					name: "智能预测控制"
				},
				{
					name: "数字信号处理"
				},
				{
					name: "模糊系统与控制"
				},
				{
					name: "模式识别"
				},
				{
					name: "泛函分析基础"
				},
				{
					name: "机器人视觉控制"
				}
			]
		},
		{
			name: "控制专硕",
			type: 5,
			required: [
				{
					name: "信息科学前沿"
				},
				{
					name: "专业数学基础（A）"
				},
				{
					name: "专业实践1"
				},
				{
					name: "信息检索"
				},
				{
					name: "知识产权"
				},
				{
					name: "专业实践讲座"
				},
				{
					name: "控制工程"
				},
				{
					name: "控制理论前沿"
				},
				{
					name: "智能机器人前沿"
				},
				{
					name: "线性系统理论（专硕）"
				},
				{
					name: "自适应控制"
				},
				{
					name: "政治"
				},
				{
					name: "自然辩证法"
				}
			],
			elective: [
				{
					name: "建模与辨识"
				},
				{
					name: "随机过程"
				},
				{
					name: "运筹学与最优化"
				},
				{
					name: "机器人学"
				},
				{
					name: "自适应控制理论及应用讨论班（1）"
				},
				{
					name: "自适应控制理论及应用讨论班（2）"
				},
				{
					name: "自适应控制理论及应"
				},
				{
					name: "智能预测控制"
				},
				{
					name: "数字信号处理"
				}
			]
		}
	]

	scheme_id = $location.search().id
	$scope.scheme = schemes[scheme_id]
	$scope.title = $scope.scheme.name

	current_page = 1

	$scope.switching = false
	$scope.questions = [
		{
			options: [
				"感兴趣，有需要"
				"讲的好"
				"给分高"
				"容易过"
				"随便选的"
				"其他"
			]
			answer: []
			other: null
		}
	]

	$scope.go_advice = ->
		$window.location = "./teac_advice.html#?id=#{scheme_id}"

	NetManager.get("/Scheme/#{scheme_id}").then (data)->
		console.log data
		if data.status
			$scope.scheme_score = data.info

	NetManager.get("/Scheme/#{scheme_id}/advices").then (data)->
		console.log data
		if data.status
			$scope.advices = data.info
			if $scope.advices.length < 10
				$scope.no_more = true

	$scope.comment_handler = ->
		$('body').scrollTop(0)
		$scope.page_state = 2
		$scope.switching = true
	
	$scope.back_handler = ->
		if $scope.switching
			$('body').scrollTop(0)
			$scope.switching = false
		else
			if $window.history.length > 1
				$window.history.back()
			else
				$window.location.href = "./welcome.html"
	
	$scope.vote = (c, action) ->
		#c : 一个advice
		if action != "up" && action != "down" then console.log "wrong action"; return
		unless c.action
			net_action = NetManager.post
		else
			net_action = NetManager.delete
		NetManager.post("/Advice/#{c.id}/#{action}").then (data)->
			console.log data
			if +data.status == 1
				#获取对应advice的up和down的数量
				NetManager.get("/Advice/#{c.id}").then (data) ->
					return if +data.status != 1
					info = data.info
					c.up = info.up
					c.down = info.down
					c.vote = info.vote

	$scope.load_more = ->
		current_page += 1
		request_param = {page: current_page}
		NetManager.get("/Scheme/#{scheme_id}/advices", request_param).then (data)->
			console.log data
			advices_arr = data.info
			$scope.advices = $scope.advices.concat(advices_arr)
			if advices_arr.length < 10
				$scope.no_more = true

	null
]);