export default {
    getFileExtension(filename) {
        return filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2);
      },
      isFileSizeCorrect(size,maxSize) {
          
          const mb = size / 1024 / 1024; //convertimos a mb y despues comparamos si se supera el maximo
          console.log(size,mb)
          return (mb < maxSize)
      },
}