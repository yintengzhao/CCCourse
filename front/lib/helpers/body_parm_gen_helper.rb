module BodyParmGenHelper
  def gen_with(item)
  	controller = item[:controller] || ""
  	parameters = item[:parameters]

  	controller_name = controller.split("_").collect{ |e| e.capitalize }.join
  	css_id = controller.gsub("_", "-")
  	ng_controller="ng-controller=" + "\"#{controller_name}Ctrl\""
  	id="id=\"#{css_id}\""
  	"#{ng_controller} #{id}"
  end
end