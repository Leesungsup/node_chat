import sys
import cv2 as cv

def getName(name, age):
    print (name + " : " + age)

if __name__ == '__main__':
    print("./uploads/"+sys.argv[3])
    img = cv.imread("./uploads/"+sys.argv[3])
    img = cv.resize(img, (350, 500))
    cv.imshow("201835494 LeeSuengsup",img)
    cv.waitKey(0)
    getName(sys.argv[1], sys.argv[2])
    print("hello")