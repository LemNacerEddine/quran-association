export const fonts = {
  // Arabic fonts
  arabic: {
    regular: 'System',
    medium: 'System',
    bold: 'System',
  },
  
  // Font sizes
  sizes: {
    xs: 12,
    sm: 14,
    md: 16,
    lg: 18,
    xl: 20,
    xxl: 24,
    xxxl: 28,
    huge: 32,
  },
  
  // Line heights
  lineHeights: {
    xs: 16,
    sm: 20,
    md: 24,
    lg: 28,
    xl: 32,
    xxl: 36,
    xxxl: 40,
    huge: 44,
  },
};

export const typography = {
  h1: {
    fontSize: fonts.sizes.huge,
    lineHeight: fonts.lineHeights.huge,
    fontWeight: 'bold' as const,
    fontFamily: fonts.arabic.bold,
  },
  h2: {
    fontSize: fonts.sizes.xxxl,
    lineHeight: fonts.lineHeights.xxxl,
    fontWeight: 'bold' as const,
    fontFamily: fonts.arabic.bold,
  },
  h3: {
    fontSize: fonts.sizes.xxl,
    lineHeight: fonts.lineHeights.xxl,
    fontWeight: '600' as const,
    fontFamily: fonts.arabic.medium,
  },
  h4: {
    fontSize: fonts.sizes.xl,
    lineHeight: fonts.lineHeights.xl,
    fontWeight: '600' as const,
    fontFamily: fonts.arabic.medium,
  },
  body1: {
    fontSize: fonts.sizes.md,
    lineHeight: fonts.lineHeights.md,
    fontWeight: 'normal' as const,
    fontFamily: fonts.arabic.regular,
  },
  body2: {
    fontSize: fonts.sizes.sm,
    lineHeight: fonts.lineHeights.sm,
    fontWeight: 'normal' as const,
    fontFamily: fonts.arabic.regular,
  },
  caption: {
    fontSize: fonts.sizes.xs,
    lineHeight: fonts.lineHeights.xs,
    fontWeight: 'normal' as const,
    fontFamily: fonts.arabic.regular,
  },
};