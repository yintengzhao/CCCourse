require 'pry'
class JSPipeline
@lib2load = ["ng_app", "constants", "net_manager", "nc-nav", 
	"nc-star", "nc-message", "application"]
class << self
	def generate
		dir = Dir.pwd
		pipe_tags = ""
		@lib2load.each do |lib|
			pipe_tags << gen_tag(lib)
		end
		Dir.glob(dir+"/content/js/coffee/*.coffee") do |fname|
			fname = File.basename(fname.split('.coffee')[0])
			next if @lib2load.include?(fname) || fname.match("application_")
			pipe_tags << gen_tag(fname)
		end
		pipe_tags
	end

	def gen_tag(file)
		"<script src=\"js/coffee-js/#{file}.js\"></script>\n\t"
	end

	def pipe_all(items, item)
		# binding.pry
		dir = Dir.pwd
		lib2load = @lib2load.dup

		Dir.glob(dir+"/content/js/coffee/*.coffee") do |fname|
			fname = File.basename(fname.split('.coffee')[0])
			next if @lib2load.include? fname
			lib2load << fname
		end
		
		js_arr = []
		for lib in lib2load
			next if lib.match "application_"
			item = items.find{|i| i.identifier == "/js/coffee/#{lib}.coffee"}
			puts "File #{lib} doesn't exist!" unless item
			js_arr << item.compiled_content
		end

		js_arr.join("\n")
	end

	def pipe_all_lib(items, item)
		# binding.pry
		dir = Dir.pwd
		lib2load = [
			"jquery/dist/jquery", 
			"angular/angular", 
			"angular-aria/angular-aria", 
			"angular-animate/angular-animate", 
			"angular-material/angular-material", 
			"angular-material-icons/angular-material-icons", 
			"intl-tel-input/build/js/intlTelInput",
			"intl-tel-input/lib/libphonenumber/build/utils",
			"international-phone-number/releases/international-phone-number",
			"angular-resource/angular-resource",
			"angular-messages/angular-messages",
			"slip.js/dist/slip",
		]

		js_arr = []
		for lib in lib2load
			_lib_file = "/lib/bower_components/#{lib}.js"
			lib_file = "/lib/bower_components/#{lib}.min.js"
			item = items.find{|i| i.identifier == lib_file} || items.find{|i| i.identifier == _lib_file}
			puts "File #{lib_file} doesn't exist!" unless item
			js_arr << item.compiled_content
		end

		js_arr.join("\n")
	end
end
end