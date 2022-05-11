# Report - Advanced Topics in Web Development 2

#### Harry Tidball (15015043)  

### **[Click here to access code and data files](https://github.com/harrytidball/ATWD2)**

### **Parsing Methods and Tools**

There are two main methods to contemplate when parsing XML documents. These methods are DOM and streaming-orientated, which operate using two different approaches to provide the user with a parsing method suitable for the size of the document. Due to the varying nature of such documents, whereby some may be small and unsophisticated, and others large and containing vast amounts of data, it is essential to choose a parsing method which will provide a solution that is optimal for the type of application in question. 

With the use of the DOM parsing method, documents are fully loaded, with a full representation of the XML tree stored in-memory. This arguably provides more flexibility to users, compared with its streaming counterpart, as the loaded document can be directly manipulated with languages such as JavaScript. This means that elements can be easily retrieved using their ID and tag names. Moreover, the document can be queried using XPath which provides a powerful mechanism for retrieving data. The validity of documents can also be verified in tandem with XML schemas, which permits a suitable method for ensuring potential errors within an XML document can be detected and rectified. SimpleXML is a core PHP function which provides a straightforward method for the manipulation of XML documents. This function is particularly useful if the user knows the layout or structure of the document, as the desired document data can be easily retrieved. On the other hand, the DOM parsing method is not compatible with large document files, as the creation process of an in-memory tree representation is particularly slow with larger XML files. 

On the other hand, streaming-orientated parsers offer a fast and reliable method for managing large XML files. The streaming process involves iterating over each element individually, which in turn completely minimises the effect of larger file sizes, as the file is not fully loaded into memory, unlike DOM-orientated methods. XMLReader is a PHP extension that provides the elements and attributes of the currently parsed line, before iterating to the next. Furthermore, XMLReader is an example of a pull parser, which provides additional flexibility in comparison to push parsers, as pull streaming enables the movement between elements to occur effortlessly. Whereas with push streaming, the parsers maintain control of the cursor, and therefore data cannot be so easily manipulated. While, streaming orientated parsers provide a suitable system for the loading of larger XML files, the lack of a fully-loaded document means that the user does not have the same freedoms in regards to data retrieval and manipulation as that of DOM-orientated parsers.

Furthermore, processor constraints may provide a situation whereby it is beneficial to load an XML document using a streaming-orientated parsing method. If an application has strict memory limitations, it may be unfeasible to utilise a DOM-orientated method, even for a file size which may usually be handled by such an approach, particularly if the application is undertaking several concurrent requests. Streaming-orientated parsing methods also assist with the restriction of the application’s memory footprint by discarding infoset elements directly after they have been used, which often increases the application’s performance, as opposed to DOM-orientated methods which provide no garbage collection operation by default. This suggests that this method would benefit applications that are looking to prioritise battery life as a requirement. Additionally, due to its iterative nature, streaming-orientated parsing methods return information during the processing of the document. This means that this method would be particularly useful for specific tasks such as locating the first set of tags in an XML document. 

To conclude, both methods of parsing provide a reliable method for the retrieval of XML data in their respective file size domains. The domain of DOM parsing is that of small-to-medium files, whereby the representation of the document can be fully loaded which in turn provides the user with a wide array of data retrieval and manipulation techniques. However, for larger files, the DOM method becomes inefficient and even implausible due to the demands of fully loading such files. Conversely, the streaming-orientated parsing method provides an alternative solution for when files are outside of the realm of DOM parsing possibilities. While, many of the freedoms of DOM parsing are not available with streaming, pull streaming methods such as XMLReader demonstrate that data retrieval and manipulation can be conducted on a larger scale. The performance gains that streaming parsers often provide suggest that its implementation is beneficial for applications whereby performance is an essential feature, for example, high-speed high-frequency trading systems.

### **Extending Charting Functionality**

To extend the functionality of the scatter chart it would be beneficial to allow the user to select a particular station to view, as opposed to only viewing the sole station chosen during development. This would allow for a comparison between each station regarding the level of pollution. Additionally, it would be insightful to provide the user with data for different pollutants, as a station may contain a low level for the displayed pollutant (NO), but contain high levels for other pollutants. This would provide additional clarity to the user and would ensure there are no false assumptions made on the level of pollution based solely on the data of NO, as currently, the user is unable to view such data using the scatter chart. Furthermore, each data point of the scatter chart could be colour coded based on the level of pollution it represents, with a red colour used to represent a high level of pollution, and a green colour for low levels.

Moreover, it would be useful to implement the scatter chart so that it shows the range of data for each month, as opposed to a monthly average. By only showing the average value of NO, it means that any instances where the pollution levels significantly increased or decreased are not visualised. These instances would be particularly useful to analyse to understand the reason for their occurrence. Instead, a scientist who may try to interpret the charts would not be provided with the information regarding these significant increases or decreases. Furthermore, the scatter chart could provide additional insight by displaying multiple years of NO data. Each year could be colour-coded to provide an understanding of the positive or negative progress that has occurred each year regarding levels of the pollutant. 

Regarding the line chart implementation, it would be beneficial to provide the user with the functionality to overlay multiple stations. This would allow for easier comparisons to be made between stations regarding their pollution levels. To implement this, it would be essential to colour-code each station, however, this would mean that the differing pollutants would not necessarily be colour-coded due to the number of colours necessary, and could therefore lead to a confusing user interface. Additionally, the line chart implementation could be improved by displaying a message to the user if the date that they have selected for a station contains no data. Currently, if no data is available for a selected date, the application displays an empty chart, which could confuse the user as they may wonder if they have encountered a bug with the application. By implementing additional messages, any confusion could quickly be clarified.

The line chart could also be improved through the smoothing of the lines, a feature of the Google Charts API. This would potentially increase the clarity of the chart as it would be easier to interpret each data pattern. Whilst this may improve the chart from a user interface perspective, it could be argued that this would also be disadvantageous as it would reduce the accuracy of the data displayed, as the chart currently displays a true rendition of every data point, even if the chart is not curved. Additionally, annotations could have been implemented to highlight the highest and lowest data points on the chart. Whilst the user can click on data points on the chart to receive information, it would be useful to display the time and pollutant type of certain data points by default to enhance the user’s understanding.	 

Furthermore, regarding the overall layout of the charts webpage, it could be beneficial to display just one chart and to allow the user to select the chart that they wish to view. This could increase the usability of the user interface as there would not be as much data displayed as there currently is. As some users may be overwhelmed with both charts provided on the same webpage. Additionally, both of the charts may benefit from the implementation of animations, which may enhance the user interface by sequentially introducing each data point. This would provide the user with greater visualisation of the positive or negative progress made in regards to the levels of pollution. Moreover, data points representing the highest and lowest levels of pollution could be continuously animated to draw the user’s awareness to these areas of the charts.

### **Reflection**

In regards to the initial learning goals and outcomes, it could be argued that all were achieved throughout the project. The modelling, cleansing and normalising of real-world big data was completed quickly and accurately through the development of two benchmarking conversion scripts. This was firstly accomplished using a divide-and-conquer strategy which helped to break the large data file into smaller, more manageable files. Each station’s data was successfully outputted into a CSV file using a CSV cleansing script. Each file contains the relevant headings whilst not including records that did not contain NOx or CO readings, as requested. Following this, the CSV files were successfully normalised to XML using the designated XML structure.

Upon completion of the XML normalisation script, an XML schema used for validation purposes was successfully developed. The schema is strict, well structured, and makes use of the available XML Schema syntax. This was followed by the implementation of a web-based charting application which utilised the Google Charts API to provide a faithful rendition of the data in both line and scatter chart formats. Furthermore, the source code created for this implementation adheres to good programming practices, such as the separation of concerns, the appropriate commenting of code, and the crediting of third-party code where necessary. 

Moreover, a web-based mapping application was implemented using the Google Maps API, which appropriately displays the data and location of each station using an interface with a high level of usability. The interface is clear, provides animations, and allows the user to select a station to view information relating to the station. Furthermore, multiple key technologies were used throughout, with PHP and XML skills being demonstrated and extended with the implementation of efficient and accurate conversion scripts. Additionally, JavaScript skills were demonstrated and extended with the implementation of the charting and mapping APIs. Finally, MARKDOWN markup syntax was learned and utilised for the presentation of the reflection and report.