require 'pry'
class CSSPipeline
	@lib2load = ["common", "nc-nav", "common_course", "common_comment"]
class << self
	def generate
		dir = Dir.pwd
		pipe_tags = ""
		@lib2load.each do |lib|
			pipe_tags << gen_tag(lib)
		end
		Dir.glob(dir+"/content/css/scss/*.scss") do |fname|
			fname = File.basename(fname.split('.scss')[0])
			next if @lib2load.include?(fname) || fname.match("application_")
			pipe_tags << gen_tag(fname)
		end
		pipe_tags
	end

	def gen_tag(file)
		" <link rel=\"stylesheet\" type=\"text/css\"" +
		"href=\"css/scss-css/#{file}.css\"/>\n\t"
	end

	def pipe_all(items, item)
		dir = Dir.pwd

		files = []

		Dir.glob(dir+"/content/css/scss/*.scss") do |fname|
			fname = File.basename(fname.split('.scss')[0])
			files << fname
		end

		files.delete "application_all"
		css_ar = []
			for file in files
				item = items.find{|i| i.identifier == "/css/scss/#{file}.scss"}
				puts "File #{file} doesn't exist!" unless item
				css_ar << item.compiled_content
		  end
		css_ar.join("\n")
	end
end
end